<?php

namespace App\Support;

class FactoryOptions
{
    public static function roles(): array
    {
        return config('factory.roles');
    }

    public static function styleCategories(): array
    {
        return [
            'formal' => 'Formal',
            'casual' => 'Casual',
            'chino' => 'Chino',
            'jeans' => 'Jeans-style',
            'cargo' => 'Cargo',
        ];
    }

    public static function fits(): array
    {
        return [
            'skinny' => 'Skinny',
            'slim' => 'Slim',
            'regular' => 'Regular',
            'tapered' => 'Tapered',
            'straight' => 'Straight',
            'relaxed' => 'Relaxed',
            'comfort' => 'Comfort',
        ];
    }

    public static function sampleStages(): array
    {
        return [
            'proto' => 'Proto',
            'fit' => 'Fit',
            'size_set' => 'Size set',
            'pp' => 'Pre-production',
        ];
    }

    public static function approval(): array
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    public static function orderStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'confirmed' => 'Confirmed',
            'in_production' => 'In production',
            'washing' => 'Washing',
            'finishing' => 'Finishing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public static function sections(): array
    {
        return [
            'cutting' => 'Cutting',
            'preparatory' => 'Preparatory',
            'front' => 'Front',
            'back' => 'Back',
            'assembly' => 'Assembly',
            'wash' => 'Wash',
            'finishing' => 'Finishing',
            'packing' => 'Packing',
            'shipped' => 'Shipped',
        ];
    }

    public static function components(): array
    {
        return [
            'front' => 'Front',
            'back' => 'Back',
            'waistband' => 'Waistband',
            'pocket' => 'Pocket',
            'fly' => 'Fly',
            'belt_loop' => 'Belt loop',
            'coin_pocket' => 'Coin pocket',
        ];
    }

    public static function poStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'partial' => 'Partial',
            'received' => 'Received',
            'closed' => 'Closed',
        ];
    }

    public static function qcResults(): array
    {
        return [
            'pending' => 'Pending',
            'passed' => 'Passed',
            'conditional' => 'Conditional',
            'failed' => 'Failed',
            'pass' => 'Pass',
            'fail' => 'Fail',
            'hold' => 'Hold',
        ];
    }

    public static function inspectionTypes(): array
    {
        return [
            'inline' => 'Inline',
            'endline' => 'End-line',
            'final_aql' => 'Final AQL',
        ];
    }

    public static function defectCategories(): array
    {
        return [
            'shade' => 'Shade',
            'measurement' => 'Measurement',
            'construction' => 'Construction',
            'stain' => 'Stain',
            'damage' => 'Damage',
            'trim' => 'Trim',
        ];
    }

    public static function severities(): array
    {
        return [
            'low' => 'Low',
            'minor' => 'Minor',
            'medium' => 'Medium',
            'major' => 'Major',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    public static function issueTypes(): array
    {
        return [
            'shade_mismatch' => 'Fabric shade mismatch',
            'bundle_mixup' => 'Bundle mix-up',
            'line_imbalance' => 'Line imbalance',
            'low_efficiency' => 'Low operator efficiency',
            'late_material' => 'Late material',
            'recurring_defect' => 'Recurring quality defect',
            'machine_downtime' => 'Machine downtime',
            'manual' => 'Manual issue',
        ];
    }

    public static function issueStatuses(): array
    {
        return [
            'open' => 'Open',
            'acknowledged' => 'Acknowledged',
            'in_progress' => 'In progress',
            'resolved' => 'Resolved',
            'dismissed' => 'Dismissed',
        ];
    }

    public static function machineStatuses(): array
    {
        return [
            'running' => 'Running',
            'idle' => 'Idle',
            'breakdown' => 'Breakdown',
            'maintenance' => 'Maintenance',
        ];
    }

    public static function trimCategories(): array
    {
        return [
            'button' => 'Button',
            'zipper' => 'Zipper',
            'thread' => 'Thread',
            'interlining' => 'Interlining',
            'label' => 'Label',
            'tag' => 'Tag',
            'polybag' => 'Polybag',
            'hanger' => 'Hanger',
            'other' => 'Other',
        ];
    }

    public static function sizes(): array
    {
        $sizes = ['28', '30', '32', '34', '36', '38', '40', '42'];

        return array_combine($sizes, $sizes);
    }

    public static function map(string $name): array
    {
        return match ($name) {
            'roles' => self::roles(),
            'style_categories' => self::styleCategories(),
            'fits' => self::fits(),
            'sample_stages' => self::sampleStages(),
            'approval' => self::approval(),
            'order_statuses' => self::orderStatuses(),
            'priorities' => self::priorities(),
            'sections' => self::sections(),
            'components' => self::components(),
            'po_statuses' => self::poStatuses(),
            'qc_results' => self::qcResults(),
            'inspection_types' => self::inspectionTypes(),
            'defect_categories' => self::defectCategories(),
            'severities' => self::severities(),
            'issue_types' => self::issueTypes(),
            'issue_statuses' => self::issueStatuses(),
            'machine_statuses' => self::machineStatuses(),
            'trim_categories' => self::trimCategories(),
            'sizes' => self::sizes(),
            default => [],
        };
    }
}
