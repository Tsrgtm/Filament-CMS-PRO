<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Nepal360\FilamentCmsPro\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static string|\UnitEnum|null $navigationGroup = 'Taxonomy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Category Hierarchy & Settings')
                    ->schema([
                        Select::make('parent_id')
                            ->relationship('parent', 'id') // will reference category ID
                            ->placeholder('No Parent Category'),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                    ]),

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
                                Textarea::make('description')
                                    ->rows(2),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'en' => 'English Category Name',
                                'np' => 'Nepali Category Name',
                                'es' => 'Spanish Category Name',
                                'zh' => 'Chinese Category Name',
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
                TextColumn::make('parent.id')
                    ->label('Parent ID')
                    ->placeholder('None'),
                TextColumn::make('order')
                    ->sortable(),
            ]);
    }
}
