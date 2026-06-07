<?php

namespace Nepal360\FilamentCmsPro\Tests\Feature;

use Nepal360\FilamentCmsPro\Tests\TestCase;
use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Models\PostTranslation;
use Nepal360\FilamentCmsPro\Models\ContentLock;
use Nepal360\FilamentCmsPro\Jobs\DeliverWebhookJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CmsEnterpriseFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_post_translation_creates_automatic_revision(): void
    {
        $post = Post::create([
            'status' => 'draft',
            'layout_template' => 'standard',
        ]);

        $translation = $post->translations()->create([
            'locale' => 'en',
            'title' => 'First Draft Title',
            'slug' => 'first-draft-title',
            'content' => [['type' => 'paragraph', 'data' => ['text' => 'Hello World']]],
        ]);

        // Check if revision exists
        $this->assertDatabaseHas('revisions', [
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => 'First Draft Title',
        ]);

        $this->assertEquals(1, $post->revisions()->count());
    }

    public function test_post_locking_mechanism_can_be_set_and_read(): void
    {
        $post = Post::create([
            'status' => 'draft',
        ]);

        $post->lock()->create([
            'user_id' => 1,
            'locked_at' => now(),
        ]);

        $this->assertNotNull($post->lock);
        $this->assertEquals(1, $post->lock->user_id);
    }

    public function test_webhook_delivery_job_sends_hmac_signature_correctly(): void
    {
        Http::fake();

        $url = 'https://my-webhook-listener.com/endpoint';
        $event = 'post.published';
        $payload = ['id' => 42, 'title' => 'Epic Post'];
        $secret = 'super-secret-key';

        // Dispatch job synchronously
        (new DeliverWebhookJob($url, $event, $payload, $secret))->handle();

        Http::assertSent(function ($request) use ($url, $secret) {
            $this->assertEquals($url, $request->url());
            
            $signature = $request->header('X-CMS-Signature')[0] ?? null;
            $this->assertNotNull($signature);

            // Recompute signature to verify
            $body = $request->body();
            $expectedSignature = hash_hmac('sha256', $body, $secret);

            return hash_equals($expectedSignature, $signature);
        });
    }
}
