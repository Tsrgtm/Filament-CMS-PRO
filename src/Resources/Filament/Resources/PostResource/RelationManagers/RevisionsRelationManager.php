<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Nepal360\FilamentCmsPro\Models\Revision;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $title = 'Revision History';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Editor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('locale')
                    ->badge()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
            ])
            ->actions([
                Action::make('restore')
                    ->label('Restore')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->action(function (Revision $record) {
                        $post = $record->post;
                        
                        // Find or create the translation for this locale
                        $translation = $post->translations()->updateOrCreate(
                            ['locale' => $record->locale],
                            [
                                'title' => $record->title,
                                'excerpt' => $record->excerpt,
                                'content' => $record->content,
                            ]
                        );

                        Notification::make()
                            ->success()
                            ->title('Revision Restored')
                            ->body("Restored \"{$record->title}\" (Locale: {$record->locale}) to the revision from {$record->created_at->format('Y-m-d H:i')}.")
                            ->send();
                    }),
            ]);
    }
}
