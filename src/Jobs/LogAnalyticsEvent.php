<?php

namespace Nepal360\FilamentCmsPro\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Nepal360\FilamentCmsPro\Models\AnalyticsPageView;

class LogAnalyticsEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AnalyticsPageView::create([
            'post_id' => $this->payload['post_id'],
            'path' => substr($this->payload['path'], 0, 1000),
            'referrer_host' => $this->payload['referrer_host'] ? substr($this->payload['referrer_host'], 0, 255) : null,
            'visitor_hash' => $this->payload['visitor_hash'],
            'is_robot' => $this->payload['is_robot'],
            'utm_source' => $this->payload['utm_source'] ? substr($this->payload['utm_source'], 0, 100) : null,
            'utm_medium' => $this->payload['utm_medium'] ? substr($this->payload['utm_medium'], 0, 100) : null,
            'utm_campaign' => $this->payload['utm_campaign'] ? substr($this->payload['utm_campaign'], 0, 100) : null,
            'viewed_at' => $this->payload['viewed_at'],
        ]);
    }
}
