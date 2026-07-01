<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Post;
use App\Models\Banner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * DashboardStats — admin dashboard overview widget.
 * Shows key metrics on the Filament dashboard.
 */
class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Products', Product::count())
                ->description('Total products')
                ->icon('heroicon-o-shopping-bag')
                ->color('success'),

            Stat::make('Posts & Pages', Post::count())
                ->description('CMS content')
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Banners', Banner::count())
                ->description('Active banners')
                ->icon('heroicon-o-photo')
                ->color('warning'),
        ];
    }
}
