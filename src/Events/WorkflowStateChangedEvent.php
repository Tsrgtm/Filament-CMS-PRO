<?php

namespace Nepal360\FilamentCmsPro\Events;

use Nepal360\FilamentCmsPro\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WorkflowStateChangedEvent
{
    use Dispatchable, SerializesModels;

    public Post $post;
    public string $oldStatus;
    public string $newStatus;
    public ?User $triggeringUser;

    /**
     * Create a new event instance.
     */
    public function __construct(Post $post, string $oldStatus, string $newStatus, ?User $user = null)
    {
        $this->post = $post;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->triggeringUser = $user;
    }
}
