<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Mount page lifecycle hook. Check and set content edit lock.
     */
    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        $post = $this->getRecord();
        $user = auth()->user();
        $userId = $user ? $user->id : 1;
        $userName = $user ? $user->name : 'Another Administrator';

        $lock = $post->lock;

        // If lock exists and is held by a different user and is less than 15 minutes old
        if ($lock && $lock->user_id !== $userId && $lock->locked_at->gt(now()->subMinutes(15))) {
            Notification::make()
                ->danger()
                ->title('Content is Locked')
                ->body("This post is currently being edited by {$userName}. Access is restricted to prevent overwriting changes.")
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        // Set or refresh lock
        $post->lock()->updateOrCreate(
            [],
            [
                'user_id' => $userId,
                'locked_at' => now(),
            ]
        );
    }

    /**
     * Clear content lock after successful save.
     */
    protected function afterSave(): void
    {
        $this->getRecord()->lock()->delete();
    }
}
