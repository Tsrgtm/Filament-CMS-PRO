<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Nepal360\FilamentCmsPro\Models\Category;
use Nepal360\FilamentCmsPro\Resources\Filament\Concerns\HasDynamicCustomFields;

class CategoryResource extends Resource
{
    use HasDynamicCustomFields;

    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_categories_enabled', true);
    }

    public static function getNavigationGroup(): ?string
    {
        return \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_group', 'CMS');
    }

    public static function getNavigationLabel(): string
    {
        return \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_categories_label', 'Categories');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
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
                                Section::make('Translatable Custom Fields')
                                    ->schema(static::getCustomFieldsSchema('categories', translatableOnly: true))
                                    ->visible(fn () => count(static::getCustomFieldsSchema('categories', translatableOnly: true)) > 0)
                                    ->collapsed(),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'en' => 'English Category Name',
                                'np' => 'Nepali Category Name',
                                'es' => 'Spanish Category Name',
                                'zh' => 'Chinese Category Name',
                                default => 'New Translation',
                            })
                    ]),
                Section::make('Custom Fields')
                    ->schema(static::getCustomFieldsSchema('categories', translatableOnly: false))
                    ->visible(fn () => count(static::getCustomFieldsSchema('categories', translatableOnly: false)) > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        $allColumns = [
            'id' => TextColumn::make('id')->sortable(),
            'name' => TextColumn::make('translations.name')
                ->label('Name')
                ->searchable(),
            'parent' => TextColumn::make('parent.id')
                ->label('Parent ID')
                ->placeholder('None'),
            'order' => TextColumn::make('order')
                ->sortable(),
        ];

        return $table
            ->columns(static::getVisibleTableColumns('categories', $allColumns));
    }
}
