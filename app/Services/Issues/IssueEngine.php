<?php

namespace App\Services\Issues;

use App\Models\Breakdown;
use App\Models\BuyerOrder;
use App\Models\Bundle;
use App\Models\BundleScan;
use App\Models\Defect;
use App\Models\FactoryIssue;
use App\Models\LinePlan;
use App\Models\ProductionLog;
use App\Models\PurchaseOrder;
use ImranDevBd\AiHub\Facades\AIHub;
use Illuminate\Support\Carbon;
use Throwable;

class IssueEngine
{
    public function scan(): int
    {
        $created = 0;

        foreach ($this->findings() as $finding) {
            if (empty($finding['factory_id'])) {
                continue;
            }

            $open = FactoryIssue::query()
                ->where('fingerprint', $finding['fingerprint'])
                ->whereIn('status', ['open', 'acknowledged', 'in_progress'])
                ->exists();

            if ($open) {
                continue;
            }

            $issue = FactoryIssue::query()->create([
                ...$finding,
                'source' => 'detector',
                'detected_at' => now(),
                'status' => 'open',
                'ai_recommendation' => $this->recommend($finding),
            ]);

            $created++;

            $rule = \App\Models\EscalationRule::query()
                ->where('issue_type', $issue->type)
                ->where('is_active', true)
                ->where('auto_create_task', true)
                ->first();

            if ($rule) {
                $issue->openActions();
            }
        }

        return $created;
    }

    public function escalate(): int
    {
        $count = 0;

        $issues = FactoryIssue::query()
            ->whereIn('status', ['open', 'acknowledged'])
            ->whereDoesntHave('actions')
            ->get();

        foreach ($issues as $issue) {
            $rule = \App\Models\EscalationRule::query()
                ->where('issue_type', $issue->type)
                ->where('is_active', true)
                ->first();

            if (! $rule || ! $issue->detected_at) {
                continue;
            }

            if ($issue->detected_at->gt(now()->subHours($rule->escalate_after_hours))) {
                continue;
            }

            if ($rule->auto_create_task) {
                $issue->openActions();
            }

            $issue->update(['status' => 'acknowledged']);
            $count++;
        }

        return $count;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findings(): array
    {
        return array_merge(
            $this->shadeMismatches(),
            $this->bundleMixups(),
            $this->lineImbalances(),
            $this->lowEfficiency(),
            $this->lateMaterials(),
            $this->recurringDefects(),
            $this->machineDowntime(),
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function shadeMismatches(): array
    {
        $issues = [];

        $bundles = Bundle::query()->whereNotNull('shade')->get()->groupBy(
            fn (Bundle $bundle) => $bundle->buyer_order_id.'|'.$bundle->color
        );

        foreach ($bundles as $key => $group) {
            $shades = $group->pluck('shade')->filter()->unique()->values();

            if ($shades->count() < 2) {
                continue;
            }

            $first = $group->first();
            $issues[] = $this->make(
                factoryId: $first->factory_id,
                department: 'cutting',
                type: 'shade_mismatch',
                severity: 'high',
                title: 'Shade split on '.$first->color.' panels',
                detail: 'Order #'.$first->buyer_order_id.' has bundles in shades '.$shades->implode(', ').'. Do not mix these panels in one garment.',
                fingerprint: 'shade:'.$key,
                actions: [
                    ['title' => 'Hold mixed-shade bundles and recut from one lot', 'owner_role' => 'cutting_master', 'detail' => 'Segregate by shade before issuing to sewing.'],
                    ['title' => 'Confirm shade band with the buyer if both lots must ship', 'owner_role' => 'merchandiser'],
                ],
                relatedType: BuyerOrder::class,
                relatedId: $first->buyer_order_id,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function bundleMixups(): array
    {
        $issues = [];

        $scans = BundleScan::query()
            ->with(['bundle', 'sewingLine'])
            ->whereDate('scanned_at', Carbon::today())
            ->get();

        foreach ($scans as $scan) {
            if (! $scan->bundle || ! $scan->sewing_line_id) {
                continue;
            }

            $plan = LinePlan::query()
                ->where('sewing_line_id', $scan->sewing_line_id)
                ->whereDate('plan_date', $scan->scanned_at->toDateString())
                ->first();

            if (! $plan || $plan->buyer_order_id === $scan->bundle->buyer_order_id) {
                continue;
            }

            $issues[] = $this->make(
                factoryId: $scan->bundle->factory_id,
                department: 'sewing',
                type: 'bundle_mixup',
                severity: 'critical',
                title: 'Bundle '.$scan->bundle->bundle_no.' scanned on the wrong line',
                detail: $scan->sewingLine?->code.' is loaded with order #'.$plan->buyer_order_id.' but the bundle belongs to order #'.$scan->bundle->buyer_order_id.'.',
                fingerprint: 'mixup:'.$scan->id,
                actions: [
                    ['title' => 'Return the bundle to the correct line and reverse the scan', 'owner_role' => 'production_manager'],
                    ['title' => 'Check the ticket colour and size before the next out-scan', 'owner_role' => 'cutting_master'],
                ],
                relatedType: Bundle::class,
                relatedId: $scan->bundle_id,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function lineImbalances(): array
    {
        $issues = [];
        $ratio = (float) config('factory.efficiency.imbalance_ratio', 1.25);

        $logs = ProductionLog::query()->whereDate('log_date', Carbon::today())->get()->groupBy('sewing_line_id');

        foreach ($logs as $lineId => $rows) {
            $bySection = $rows->groupBy('section')->map(fn ($group) => (int) $group->sum('actual_qty'));

            if ($bySection->count() < 2) {
                continue;
            }

            $min = max(1, (int) $bySection->min());
            $max = (int) $bySection->max();

            if (($max / $min) < $ratio) {
                continue;
            }

            $first = $rows->first();
            $slow = $bySection->sort()->keys()->first();
            $issues[] = $this->make(
                factoryId: $first->factory_id,
                department: 'sewing',
                type: 'line_imbalance',
                severity: 'high',
                title: 'Line imbalance on line #'.$lineId,
                detail: 'Section output today ranges from '.$min.' to '.$max.' pieces. '.$slow.' is the bottleneck.',
                fingerprint: 'imbalance:'.$lineId.':'.Carbon::today()->toDateString(),
                actions: [
                    ['title' => 'Move a helper onto '.$slow.' for the rest of the shift', 'owner_role' => 'production_manager'],
                    ['title' => 'Rebalance the operation bulletin before tomorrow\'s load', 'owner_role' => 'production_manager'],
                ],
                relatedType: \App\Models\SewingLine::class,
                relatedId: (int) $lineId,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function lowEfficiency(): array
    {
        $issues = [];
        $threshold = (float) config('factory.efficiency.low_threshold', 70);

        $logs = ProductionLog::query()
            ->whereDate('log_date', Carbon::today())
            ->where('actual_qty', '>', 0)
            ->where('efficiency', '>', 0)
            ->where('efficiency', '<', $threshold)
            ->get();

        foreach ($logs as $log) {
            $issues[] = $this->make(
                factoryId: $log->factory_id,
                department: 'sewing',
                type: 'low_efficiency',
                severity: 'medium',
                title: 'Efficiency '.$log->efficiency.'% on '.$log->section,
                detail: 'Line #'.$log->sewing_line_id.' produced '.$log->actual_qty.' against a target of '.$log->target_qty.' at SMV '.$log->smv.'.',
                fingerprint: 'efficiency:'.$log->id,
                actions: [
                    ['title' => 'Check machine downtime and bundle waiting time on this section', 'owner_role' => 'production_manager'],
                    ['title' => 'Compare attendance punches with the manned operators', 'owner_role' => 'hr'],
                ],
                relatedType: ProductionLog::class,
                relatedId: $log->id,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function lateMaterials(): array
    {
        $issues = [];

        $orders = PurchaseOrder::query()
            ->whereNotNull('expected_date')
            ->whereDate('expected_date', '<', Carbon::today())
            ->whereNotIn('status', ['received', 'closed'])
            ->get();

        foreach ($orders as $order) {
            $issues[] = $this->make(
                factoryId: $order->factory_id,
                department: 'store',
                type: 'late_material',
                severity: 'high',
                title: 'Material PO '.$order->po_number.' is late',
                detail: 'Expected '.$order->expected_date->toDateString().' and status is still '.$order->status.'. Sewing will idle if this feeds an open order.',
                fingerprint: 'late-po:'.$order->id,
                actions: [
                    ['title' => 'Chase the supplier and revise the ex-factory TNA', 'owner_role' => 'merchandiser'],
                    ['title' => 'Reallocate available rolls or trims if a substitute lot is approved', 'owner_role' => 'store'],
                ],
                relatedType: PurchaseOrder::class,
                relatedId: $order->id,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function recurringDefects(): array
    {
        $issues = [];
        $min = (int) config('factory.quality.recurring_defect_count', 3);
        $days = (int) config('factory.quality.recurring_window_days', 7);

        $defects = Defect::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->get()
            ->groupBy(fn (Defect $defect) => $defect->buyer_order_id.'|'.$defect->code);

        foreach ($defects as $key => $group) {
            $qty = (int) $group->sum('quantity');

            if ($qty < $min) {
                continue;
            }

            $first = $group->first();
            $cause = $this->suggestCause($first);
            $issues[] = $this->make(
                factoryId: $first->inspection?->factory_id ?? $first->buyerOrder?->factory_id,
                department: 'quality',
                type: 'recurring_defect',
                severity: $first->severity === 'critical' ? 'critical' : 'high',
                title: 'Recurring defect '.$first->code.' ('.$first->name.')',
                detail: $qty.' pieces in '.$days.' days. Suggested cause: '.$cause,
                fingerprint: 'defect:'.$key.':'.now()->startOfWeek()->toDateString(),
                actions: [
                    ['title' => $cause, 'owner_role' => 'qc'],
                    ['title' => 'Add the defect to the inline checkpoint for this style', 'owner_role' => 'qc'],
                ],
                relatedType: Defect::class,
                relatedId: $first->id,
            );
        }

        return $issues;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function machineDowntime(): array
    {
        $issues = [];

        $open = Breakdown::query()->where('status', 'open')->with('machine')->get();

        foreach ($open as $breakdown) {
            $issues[] = $this->make(
                factoryId: $breakdown->factory_id,
                department: 'maintenance',
                type: 'machine_downtime',
                severity: 'high',
                title: 'Machine '.($breakdown->machine->code ?? '#'.$breakdown->machine_id).' is down',
                detail: $breakdown->reason ?: 'Breakdown is still open.',
                fingerprint: 'breakdown:'.$breakdown->id,
                actions: [
                    ['title' => 'Assign a mechanic and log the repair before the next hourly count', 'owner_role' => 'production_manager'],
                ],
                relatedType: Breakdown::class,
                relatedId: $breakdown->id,
            );
        }

        $repeat = Breakdown::query()
            ->where('reported_at', '>=', now()->subDays(14))
            ->get()
            ->groupBy('machine_id');

        foreach ($repeat as $machineId => $group) {
            if ($group->count() < 2) {
                continue;
            }

            $first = $group->first();
            $issues[] = $this->make(
                factoryId: $first->factory_id,
                department: 'maintenance',
                type: 'machine_downtime',
                severity: 'medium',
                title: 'Repeated breakdowns on machine #'.$machineId,
                detail: $group->count().' breakdowns in 14 days. The line efficiency drop is likely mechanical, not skill.',
                fingerprint: 'repeat-machine:'.$machineId.':'.now()->format('Y-m'),
                actions: [
                    ['title' => 'Pull the machine for preventive maintenance instead of running it through the week', 'owner_role' => 'production_manager'],
                ],
                relatedType: \App\Models\Machine::class,
                relatedId: (int) $machineId,
            );
        }

        return $issues;
    }

    public function suggestCause(Defect $defect): string
    {
        return match ($defect->category) {
            'shade' => 'Panels were cut from more than one shade lot. Re-match the bundle before assembly.',
            'measurement' => 'Check the pattern, marker shrinkage, and the end-line measurement spec for this size.',
            'construction' => 'Retrain the operation and confirm needle, thread, and SPI before the next bundle.',
            'stain' => 'Trace oil from the machine or wash chemical residue before packing.',
            'trim' => 'Stop the line until the trim lot matches the approved tech pack.',
            default => 'Review the last ten bundles at the operation that feeds this defect.',
        };
    }

    /**
     * @param  array<string, mixed>  $finding
     */
    protected function recommend(array $finding): ?string
    {
        if (! config('factory.ai.enabled')) {
            return null;
        }

        try {
            $response = AIHub::system('You are a trouser factory industrial engineer. Give one short corrective paragraph a floor manager can act on today.')
                ->prompt($finding['title']."\n".$finding['detail'])
                ->send();

            return $response->success ? $response->content : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  list<array<string, mixed>>  $actions
     * @return array<string, mixed>
     */
    protected function make(
        ?int $factoryId,
        string $department,
        string $type,
        string $severity,
        string $title,
        string $detail,
        string $fingerprint,
        array $actions,
        ?string $relatedType,
        ?int $relatedId,
    ): array {
        return [
            'factory_id' => $factoryId,
            'department' => $department,
            'type' => $type,
            'severity' => $severity,
            'title' => $title,
            'detail' => $detail,
            'fingerprint' => $fingerprint,
            'suggested_actions' => $actions,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ];
    }
}
