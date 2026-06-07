<?php

namespace Nepal360\FilamentCmsPro\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Nepal360\FilamentCmsPro\FilamentCmsProServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentCmsProServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
