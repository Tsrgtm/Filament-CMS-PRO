<?php

namespace Nepal360\FilamentCmsPro;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\CategoryResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\TagResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\PollResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\AdCampaignResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\WebhookResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\CommentResource;
use Nepal360\FilamentCmsPro\Resources\Filament\Pages\AnalyticsDashboard;
use Nepal360\FilamentCmsPro\Resources\Filament\Pages\ManageCmsSettings;
use Nepal360\FilamentCmsPro\Resources\Filament\Widgets\RealtimeAnalyticsWidget;

class FilamentCmsProPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-cms-pro';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PostResource::class,
                CategoryResource::class,
                TagResource::class,
                PollResource::class,
                AdCampaignResource::class,
                WebhookResource::class,
                CommentResource::class,
            ])
            ->pages([
                AnalyticsDashboard::class,
                ManageCmsSettings::class,
            ])
            ->widgets([
                RealtimeAnalyticsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
