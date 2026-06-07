<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Pages;

use Filament\Pages\Page;
use Nepal360\FilamentCmsPro\Resources\Filament\Widgets\RealtimeAnalyticsWidget;

class AnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament-cms-pro::pages.analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            RealtimeAnalyticsWidget::class,
        ];
    }
}
