<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        return [
            Stat::make('New orders', Order::query()->where('status', 'new')->count())
                ->description('Waiting for confirmation')
                ->color('info'),
            Stat::make('Pending delivery', Order::query()->whereIn('status', ['confirmed', 'processing', 'packed', 'dispatched'])->count())
                ->description('Confirmed through dispatched')
                ->color('warning'),
            Stat::make('Low stock', ProductVariant::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count())
                ->description('Variants at threshold')
                ->color('danger'),
            Stat::make('Products', Product::query()->count())
                ->description(Product::query()->where('is_active', true)->count().' active')
                ->color('success'),
        ];
    }
}
