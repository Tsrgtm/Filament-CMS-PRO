<?php

namespace Nepal360\FilamentCmsPro\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $url,
        protected string $event,
        protected array $payload,
        protected ?string $secret = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $body = [
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
            'data' => $this->payload,
        ];

        $jsonPayload = json_encode($body);
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'FilamentCmsPro-Webhook-Deliverer/1.0',
        ];

        if ($this->secret) {
            $headers['X-CMS-Signature'] = hash_hmac('sha256', $jsonPayload, $this->secret);
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($this->url, $body);

            if ($response->failed()) {
                throw new \Exception("Webhook failed with status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::warning("CMS Webhook Delivery Failed to {$this->url}: " . $e->getMessage());
            throw $e;
        }
    }
}
