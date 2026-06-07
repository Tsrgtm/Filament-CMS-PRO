<?php

namespace Nepal360\FilamentCmsPro\Tests\Feature;

use Nepal360\FilamentCmsPro\Tests\TestCase;
use Nepal360\FilamentCmsPro\Http\Middleware\TrackVisitorAnalytics;
use Nepal360\FilamentCmsPro\Jobs\LogAnalyticsEvent;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class AnalyticsTrackingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['filament-cms-pro.analytics.enabled' => true]);
    }

    public function test_rendering_public_route_dispatches_analytics_event_to_queue(): void
    {
        Queue::fake();

        // Register dummy web route
        Route::get('/test-posts/swiss-alps', function () {
            return response('Success', 200);
        })->middleware(TrackVisitorAnalytics::class);

        // Perform request
        $response = $this->get('/test-posts/swiss-alps');

        $response->assertStatus(200);

        // Verify that the queue job is dispatched asynchronously
        Queue::assertPushed(LogAnalyticsEvent::class);
    }
}
