<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Nepal360\FilamentCmsPro\Models\Tag;
use Nepal360\FilamentCmsPro\Resources\Filament\Concerns\HasDynamicCustomFields;

class TagResource extends Resource
{
    use HasDynamicCustomFields;

    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_tags_enabled', true);
    }

    public static function getNavigationGroup(): ?string
    {
        return \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_group', 'CMS');
    }

    public static function getNavigationLabel(): string
    {
        return \Nepal360\FilamentCmsPro\Models\CmsSetting::get('navigation_tags_label', 'Tags');
    }

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
                                Section::make('Translatable Custom Fields')
                                    ->schema(static::getCustomFieldsSchema('tags', translatableOnly: true))
                                    ->visible(fn () => count(static::getCustomFieldsSchema('tags', translatableOnly: true)) > 0)
                                    ->collapsed(),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'en' => 'English Tag Name',
                                'np' => 'Nepali Tag Name',
                                'es' => 'Spanish Tag Name',
                                'zh' => 'Chinese Tag Name',
                                default => 'New Translation',
                            })
                    ]),
                Section::make('Custom Fields')
                    ->schema(static::getCustomFieldsSchema('tags', translatableOnly: false))
                    ->visible(fn () => count(static::getCustomFieldsSchema('tags', translatableOnly: false)) > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        $allColumns = [
            'id' => TextColumn::make('id')->sortable(),
            'name' => TextColumn::make('translations.name')
                ->label('Name')
                ->searchable(),
        ];

        return $table
            ->columns(static::getVisibleTableColumns('tags', $allColumns));
    }
}
