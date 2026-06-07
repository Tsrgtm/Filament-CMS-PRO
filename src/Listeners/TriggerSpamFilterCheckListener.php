<?php

namespace Nepal360\FilamentCmsPro\Listeners;

use Nepal360\FilamentCmsPro\Events\CommentSubmittedEvent;
use Nepal360\FilamentCmsPro\Comments\SpamFilterEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TriggerSpamFilterCheckListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected SpamFilterEngine $spamFilter;

    public function __construct(SpamFilterEngine $spamFilter)
    {
        $this->spamFilter = $spamFilter;
    }

    /**
     * Handle the event.
     */
    public function handle(CommentSubmittedEvent $event): void
    {
        $comment = $event->comment;

        if (!config('filament-cms-pro.comments.spam_check', true)) {
            $comment->update(['status' => 'approved']);
            return;
        }

        $isSpam = $this->spamFilter->analyze($comment->content, [
            'ip' => $comment->ip_address,
            'user_agent' => $comment->user_agent,
            'name' => $comment->author_name,
            'email' => $comment->author_email,
        ]);

        if ($isSpam) {
            $comment->update(['status' => 'spam']);
        } else {
            if (config('filament-cms-pro.comments.auto_approve', false)) {
                $comment->update(['status' => 'approved']);
            }
        }
    }
}
