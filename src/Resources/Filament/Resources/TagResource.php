<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Nepal360\FilamentCmsPro\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Taxonomy';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Translations')
                    ->schema([
                        Repeater::make('translations')
                            ->relationship('translations')
                            ->schema([
                                Select::make('locale')
                                    ->options([
                                        'en' => 'English',
                                        'np' => 'Nepali',
                                        'es' => 'Spanish',
                                        'zh' => 'Chinese',
                                    ])
                                    ->required()
                                    ->distinct(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', str($state)->slug())),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'en' => 'English Tag Name',
                                'np' => 'Nepali Tag Name',
                                'es' => 'Spanish Tag Name',
                                'zh' => 'Chinese Tag Name',
                                default => 'New Translation',
                            })
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('translations.name')
                    ->label('Name')
                    ->searchable(),
            ]);
    }
}
