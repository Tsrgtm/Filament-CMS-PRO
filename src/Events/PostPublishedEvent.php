<?php

namespace Nepal360\FilamentCmsPro\Events;

use Nepal360\FilamentCmsPro\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPublishedEvent
{
    use Dispatchable, SerializesModels;

    public Post $post;

    /**
     * Create a new event instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
