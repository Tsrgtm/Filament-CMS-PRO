<?php

namespace Nepal360\FilamentCmsPro;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use Nepal360\FilamentCmsPro\Support\CmsFacade;
use Nepal360\FilamentCmsPro\SEO\Commands\GenerateSitemapsCommand;

class FilamentCmsProServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-cms-pro.php', 'filament-cms-pro'
        );

        // Bind Facade class
        $this->app->singleton('cms-engine', function ($app) {
            return new \Nepal360\FilamentCmsPro\Support\CmsEngine();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish Configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filament-cms-pro.php' => config_path('filament-cms-pro.php'),
            ], 'filament-cms-pro-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'filament-cms-pro-migrations');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-cms-pro'),
            ], 'filament-cms-pro-views');

            // Register commands
            $this->commands([
                GenerateSitemapsCommand::class,
            ]);
        }

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-cms-pro');

        // Load Routes
        $this->registerRoutes();

        // Register Event Listeners
        $this->registerEventListeners();
    }

    /**
     * Register routing contracts.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'api/v1',
            'middleware' => ['api'],
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        Route::group([
            'middleware' => ['web'],
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Register Event and Listeners mapping.
     */
    protected function registerEventListeners(): void
    {
        Event::listen(
            \Nepal360\FilamentCmsPro\Events\PostPublishedEvent::class,
            \Nepal360\FilamentCmsPro\Listeners\ClearRenderedPostCacheListener::class
        );

        Event::listen(
            \Nepal360\FilamentCmsPro\Events\WorkflowStateChangedEvent::class,
            \Nepal360\FilamentCmsPro\Listeners\SendWorkflowNotificationListener::class
        );

        Event::listen(
            \Nepal360\FilamentCmsPro\Events\CommentSubmittedEvent::class,
            \Nepal360\FilamentCmsPro\Listeners\TriggerSpamFilterCheckListener::class
        );
    }
}
