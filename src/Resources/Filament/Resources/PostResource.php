<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource\Pages;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\PostResource\RelationManagers\RevisionsRelationManager;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Left Column: Writing workspace & translations
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Multi-lingual Translations & Gutenberg visual Editor')
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
                                                TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->reactive()
                                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', str($state)->slug())),
                                                TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('excerpt')
                                                    ->rows(2)
                                                    ->maxLength(1000),
                                                
                                                // Gutenberg Block Builder
                                                Builder::make('content')
                                                    ->blocks([
                                                        Block::make('paragraph')
                                                            ->schema([
                                                                Textarea::make('text')->required()->rows(3),
                                                            ]),
                                                        Block::make('heading')
                                                            ->schema([
                                                                TextInput::make('text')->required(),
                                                                Select::make('level')
                                                                    ->options([
                                                                        1 => 'H1',
                                                                        2 => 'H2',
                                                                        3 => 'H3',
                                                                        4 => 'H4',
                                                                    ])->required(),
                                                            ]),
                                                        Block::make('image')
                                                            ->schema([
                                                                FileUpload::make('image_path')->image()->required(),
                                                                TextInput::make('alt')->label('Alt Text'),
                                                                TextInput::make('caption')->label('Image Caption'),
                                                            ]),
                                                        Block::make('cta')
                                                            ->label('Call To Action')
                                                            ->schema([
                                                                TextInput::make('text')->required()->label('Button Text'),
                                                                TextInput::make('url')->url()->required()->label('Target URL'),
                                                                Select::make('style')
                                                                    ->options([
                                                                        'primary' => 'Primary Gradient',
                                                                        'secondary' => 'Secondary Flat',
                                                                    ])->required(),
                                                            ]),
                                                        Block::make('faq')
                                                            ->label('Accordion FAQ List')
                                                            ->schema([
                                                                Repeater::make('items')
                                                                    ->schema([
                                                                        TextInput::make('question')->required(),
                                                                        Textarea::make('answer')->required()->rows(2),
                                                                    ])->minItems(1),
                                                            ]),
                                                        Block::make('tiktok')
                                                            ->label('TikTok Embed')
                                                            ->schema([
                                                                TextInput::make('embed_url')
                                                                    ->url()
                                                                    ->required()
                                                                    ->placeholder('https://www.tiktok.com/@user/video/123456789'),
                                                            ]),
                                                    ])->collapsible()->collapsed(),

                                                Section::make('SEO Metadata')
                                                    ->schema([
                                                        TextInput::make('seo_title'),
                                                        Textarea::make('seo_description')->rows(2),
                                                        Repeater::make('seo_keywords')
                                                            ->simple(TextInput::make('keyword')),
                                                    ])->collapsed()
                                            ])
                                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                                'en' => 'English Version',
                                                'np' => 'Nepali Version',
                                                'es' => 'Spanish Version',
                                                'zh' => 'Chinese Version',
                                                default => 'New Translation',
                                            })
                                            ->grid(1)
                                    ]),
                            ]),

                        // Right Column: Metadata sidebar panels
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Status & Publishing')
                                    ->schema([
                                        Select::make('status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'review' => 'Review',
                                                'fact_check' => 'Fact Check',
                                                'editor_approved' => 'Editor Approved',
                                                'publisher_approved' => 'Publisher Approved',
                                                'published' => 'Published',
                                                'rejected' => 'Rejected',
                                                'archived' => 'Archived',
                                            ])->default('draft')->required(),
                                        DateTimePicker::make('published_at'),
                                        Toggle::make('sitemap_enabled')
                                            ->default(true),
                                    ]),

                                Section::make('Authors & Taxonomy')
                                    ->schema([
                                        Select::make('authors')
                                            ->multiple()
                                            ->relationship('authors', 'name')
                                            ->preload(),
                                        Select::make('categories')
                                            ->multiple()
                                            ->relationship('categories', 'id') // category names are stored in translation table, display order or id is fine
                                            ->preload(),
                                        Select::make('tags')
                                            ->multiple()
                                            ->relationship('tags', 'id')
                                            ->preload(),
                                    ]),

                                Section::make('Layout & Comments')
                                    ->schema([
                                        Select::make('layout_template')
                                            ->options([
                                                'standard' => 'Standard Article Layout',
                                                'fullwidth' => 'Full-Width Layout',
                                                'narrow' => 'Narrow Layout',
                                            ])->default('standard')->required(),
                                        Select::make('comment_rules')
                                            ->options([
                                                'allow' => 'Allow all comments',
                                                'moderated' => 'Require manual approval',
                                                'disabled' => 'Disable comments',
                                            ])->default('allow'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('featured_image'),
                TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'review',
                        'success' => 'published',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RevisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
