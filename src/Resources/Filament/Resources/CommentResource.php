<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Nepal360\FilamentCmsPro\Models\Comment;
use Nepal360\FilamentCmsPro\Resources\Filament\Concerns\HasDynamicCustomFields;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\CommentResource\Pages;

class CommentResource extends Resource
{
    use HasDynamicCustomFields;

    protected static ?string $model = Comment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getNavigationGroup(): ?string
    {
        return \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_group', 'CMS');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Comment Info')
                    ->schema([
                        TextInput::make('author_name')->disabled(),
                        TextInput::make('author_email')->disabled(),
                        Textarea::make('content')->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                                'rejected' => 'Rejected',
                            ])->required(),
                    ]),
                Section::make('Custom Fields')
                    ->schema(static::getCustomFieldsSchema('comments', translatableOnly: false))
                    ->visible(fn () => count(static::getCustomFieldsSchema('comments', translatableOnly: false)) > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        $allColumns = [
            'id' => TextColumn::make('id')->sortable(),
            'post' => TextColumn::make('post.translations.title')->label('Post Title')->searchable()->limit(30),
            'author' => TextColumn::make('author_name')
                ->label('Author')
                ->state(fn (Comment $record) => $record->author_name ?: $record->author_email)
                ->searchable(),
            'content' => TextColumn::make('content')->label('Content Excerpt')->limit(50),
            'status' => TextColumn::make('status')
                ->badge()
                ->colors([
                    'gray' => 'pending',
                    'success' => 'approved',
                    'danger' => 'spam',
                    'warning' => 'rejected',
                ]),
            'created_at' => TextColumn::make('created_at')->dateTime()->sortable(),
        ];

        return $table
            ->columns(static::getVisibleTableColumns('comments', $allColumns))
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record) => $record->status !== 'approved')
                    ->action(fn (Comment $record) => $record->update(['status' => 'approved'])),
                Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record) => $record->status !== 'spam')
                    ->action(fn (Comment $record) => $record->update(['status' => 'spam'])),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
