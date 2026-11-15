<?php

namespace App\Filament\Pages;

use App\Models\Bundle;
use App\Models\BundleScan;
use App\Models\LinePlan;
use App\Models\SewingLine;
use App\Support\FactoryAccess;
use App\Support\FactoryOptions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class FloorScan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Sewing Floor';

    protected static ?string $navigationLabel = 'Bundle scan';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.floor-scan';

    public ?array $data = [];

    public ?string $result = null;

    public static function canAccess(): bool
    {
        return FactoryAccess::allows(auth()->user(), 'sewing', ['production_manager', 'cutting_master', 'qc']);
    }

    public function mount(): void
    {
        $this->form->fill([
            'direction' => 'in',
            'section' => 'preparatory',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('qr_code')->label('Bundle QR')->required()->autofocus(),
            Select::make('direction')->options(['in' => 'In', 'out' => 'Out'])->required(),
            Select::make('section')->options(FactoryOptions::sections())->required(),
            Select::make('sewing_line_id')->label('Line')->options(fn () => SewingLine::query()->orderBy('code')->pluck('code', 'id'))->required(),
        ])->statePath('data');
    }

    public function scan(): void
    {
        $state = $this->form->getState();
        $bundle = Bundle::query()->where('qr_code', $state['qr_code'])->first();

        if (! $bundle) {
            Notification::make()->title('No bundle with that code')->danger()->send();
            $this->result = 'Unknown code '.$state['qr_code'];

            return;
        }

        $plan = LinePlan::query()
            ->where('sewing_line_id', $state['sewing_line_id'])
            ->whereDate('plan_date', today())
            ->first();

        $warning = $plan && $plan->buyer_order_id !== $bundle->buyer_order_id
            ? ' This line is loaded with a different order. The issue scan will flag the mix-up.'
            : '';

        BundleScan::query()->create([
            'bundle_id' => $bundle->id,
            'sewing_line_id' => $state['sewing_line_id'],
            'direction' => $state['direction'],
            'section' => $state['section'],
            'scanned_at' => now(),
            'quantity' => $bundle->quantity,
            'note' => trim($warning) ?: null,
        ]);

        $bundle->update([
            'current_section' => $state['section'],
            'status' => $state['direction'] === 'out' ? 'completed' : 'in_process',
        ]);

        $this->result = $bundle->bundle_no.' · '.$bundle->color.' / '.$bundle->size.' · '.$state['direction'].' '.$state['section'].'.'.$warning;
        Notification::make()->title('Bundle recorded')->body($this->result)->success()->send();
        $this->data['qr_code'] = null;
    }
}
