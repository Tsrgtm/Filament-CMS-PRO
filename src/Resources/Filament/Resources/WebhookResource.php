<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Nepal360\FilamentCmsPro\Models\Webhook;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Webhook Endpoint Configuration')
                    ->schema([
                        TextInput::make('url')
                            ->url()
                            ->required()
                            ->maxLength(1000),
                        TextInput::make('secret')
                            ->password()
                            ->helperText('Used to sign webhook payloads using HMAC-SHA256 in the X-CMS-Signature header.')
                            ->maxLength(255),
                        Select::make('events')
                            ->multiple()
                            ->options([
                                'post.created' => 'Post Created',
                                'post.updated' => 'Post Updated',
                                'post.deleted' => 'Post Deleted',
                                'post.published' => 'Post Published',
                                'workflow.state_changed' => 'Workflow State Changed',
                            ])
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('url')->searchable()->limit(50),
                TextColumn::make('events')
                    ->badge()
                    ->separator(','),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ]);
    }
}
