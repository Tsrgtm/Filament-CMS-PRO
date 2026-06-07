<?php

namespace Nepal360\FilamentCmsPro\Listeners;

use Nepal360\FilamentCmsPro\Events\PostPublishedEvent;
use Illuminate\Support\Facades\Cache;

class ClearRenderedPostCacheListener
{
    /**
     * Handle the event.
     */
    public function handle(PostPublishedEvent $event): void
    {
        $post = $event->post;

        // Invalidate dynamic article caches
        $locale = app()->getLocale();
        Cache::forget("cms_post_{$post->id}_{$locale}_rendered");
        Cache::forget("cms_api_post_" . ($post->translation->slug ?? $post->id) . "_{$locale}");
    }
}
