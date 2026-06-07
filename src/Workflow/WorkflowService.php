<?php

namespace Nepal360\FilamentCmsPro\Workflow;

use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Events\WorkflowStateChangedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    /**
     * Transition a post to a target status, applying guards and authorization checks.
     */
    public function transitionTo(Post $post, string $targetStatus, array $options = []): void
    {
        $user = auth()->user();
        $oldStatus = $post->status;

        // Skip validations if workflow is disabled in configuration
        if (!config('filament-cms-pro.workflow.enabled', true)) {
            $this->updateStatus($post, $targetStatus, $oldStatus, $user);
            return;
        }

        // Apply pre-transition validation guards
        $this->validateTransition($post, $oldStatus, $targetStatus, $options);

        // Update database record
        $this->updateStatus($post, $targetStatus, $oldStatus, $user, $options['notes'] ?? null);
    }

    /**
     * Validate transition rules.
     */
    protected function validateTransition(Post $post, string $from, string $to, array $options): void
    {
        // Allowed transitions list
        $allowed = [
            'draft' => ['review'],
            'review' => ['fact_check', 'rejected'],
            'fact_check' => ['editor_approved'],
            'editor_approved' => ['publisher_approved'],
            'publisher_approved' => ['published', 'scheduled'],
            'scheduled' => ['published'],
            'published' => ['archived'],
            'rejected' => ['draft'],
        ];

        if (!isset($allowed[$from]) || !in_array($to, $allowed[$from])) {
            throw ValidationException::withMessages([
                'status' => ["Transition from {$from} to {$to} is not allowed."],
            ]);
        }

        // Specific validation checks
        if ($to === 'review') {
            $translation = $post->translation;
            if (!$translation || empty($translation->title)) {
                throw ValidationException::withMessages([
                    'title' => ['Post title is required to submit for review.'],
                ]);
            }
            if (empty($translation->content) || count($translation->content) === 0) {
                throw ValidationException::withMessages([
                    'content' => ['Post must contain at least one block of content to submit for review.'],
                ]);
            }
        }

        if ($to === 'rejected' && empty($options['notes'])) {
            throw ValidationException::withMessages([
                'notes' => ['Rejection notes are required to reject a post.'],
            ]);
        }

        if ($to === 'editor_approved') {
            if ($post->fact_check_status === 'unverified') {
                throw ValidationException::withMessages([
                    'fact_check_status' => ['Post cannot be approved while fact-checking status is unverified.'],
                ]);
            }
        }

        if ($to === 'scheduled' && (!$post->published_at || $post->published_at->isPast())) {
            throw ValidationException::withMessages([
                'published_at' => ['Scheduled posts must have a future publication date.'],
            ]);
        }
    }

    /**
     * Update status transactionally.
     */
    protected function updateStatus(Post $post, string $to, string $from, $user, ?string $notes = null): void
    {
        DB::transaction(function () use ($post, $to, $from, $user, $notes) {
            $updateData = ['status' => $to];
            if ($to === 'published' && !$post->published_at) {
                $updateData['published_at'] = now();
            }

            $post->update($updateData);

            // Save notes if provided
            if ($notes) {
                $translation = $post->translation;
                if ($translation) {
                    if ($to === 'rejected') {
                        $translation->update(['editorial_notes' => $notes]);
                    } else {
                        $translation->update(['publisher_notes' => $notes]);
                    }
                }
            }

            // Dispatch workflow notification event
            event(new WorkflowStateChangedEvent($post, $from, $to, $user));
        });
    }
}
