<?php

namespace Nepal360\FilamentCmsPro\Listeners;

use Nepal360\FilamentCmsPro\Events\WorkflowStateChangedEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWorkflowNotificationListener
{
    /**
     * Handle the event.
     */
    public function handle(WorkflowStateChangedEvent $event): void
    {
        $post = $event->post;
        $slackUrl = config('filament-cms-pro.workflow.slack_webhook_url');

        if ($slackUrl) {
            try {
                Http::post($slackUrl, [
                    'text' => sprintf(
                        "CMS Alert: Post *\"%s\"* (ID: %d) status changed from `%s` to `%s` by %s.",
                        $post->translation->title ?? 'Untitled',
                        $post->id,
                        $event->oldStatus,
                        $event->newStatus,
                        $event->triggeringUser ? $event->triggeringUser->name : 'System Scheduler'
                    )
                ]);
            } catch (\Exception $e) {
                Log::error('CMS Slack notification failed: ' . $e->getMessage());
            }
        }
    }
}
