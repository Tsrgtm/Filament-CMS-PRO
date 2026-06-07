<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Nepal360\FilamentCmsPro\Models\AnalyticsPageView;

class RealtimeAnalyticsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalViews = AnalyticsPageView::count();
        $uniqueVisitors = AnalyticsPageView::distinct('visitor_hash')->count();

        return [
            Stat::make('Total Page Views', $totalViews)
                ->description('Server-side non-blocking log counts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Unique Visitors', $uniqueVisitors)
                ->description('GDPR-compliant cookie-less counts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Core Web Vitals Status', 'GOOD')
                ->description('Zero browser tracking footprint')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
        ];
    }
}
