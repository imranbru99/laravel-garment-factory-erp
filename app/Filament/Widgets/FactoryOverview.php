<?php

namespace App\Filament\Widgets;

use App\Models\BuyerOrder;
use App\Models\FactoryIssue;
use App\Models\ProductionLog;
use App\Models\Trim;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FactoryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $openOrders = BuyerOrder::query()->whereNotIn('status', ['shipped', 'closed', 'cancelled'])->count();
        $output = (int) ProductionLog::query()->whereDate('log_date', today())->sum('actual_qty');
        $efficiency = ProductionLog::query()->whereDate('log_date', today())->where('efficiency', '>', 0)->avg('efficiency');
        $issues = FactoryIssue::query()->whereIn('status', ['open', 'acknowledged', 'in_progress'])->count();
        $lowStock = Trim::query()->whereColumn('stock_qty', '<=', 'reorder_level')->count();

        return [
            Stat::make('Open orders', $openOrders)->description('Not yet shipped')->color('primary'),
            Stat::make('Today output', number_format($output).' pcs')->description('All sewing sections'),
            Stat::make('Line efficiency', $efficiency ? number_format((float) $efficiency, 1).'%' : '—')->description('Pieces × SMV / available minutes'),
            Stat::make('Open issues', $issues)->description('Shade, line, material, quality, machines')->color($issues > 0 ? 'danger' : 'success'),
            Stat::make('Low trim stock', $lowStock)->description('At or under reorder level')->color($lowStock > 0 ? 'warning' : 'success'),
        ];
    }
}
