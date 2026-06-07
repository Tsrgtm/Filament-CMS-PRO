<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Notifications\Notification;
use Nepal360\FilamentCmsPro\Models\CmsSetting;

class ManageCmsSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'CMS Settings';

    protected static ?string $title = 'CMS Settings';

    protected string $view = 'filament-cms-pro::pages.manage-cms-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return CmsSetting::get('navigation_group', 'Settings');
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $keys = [
            'navigation_group',
            'navigation_categories_enabled',
            'navigation_categories_label',
            'navigation_tags_enabled',
            'navigation_tags_label',
            'posts_columns',
            'posts_filters',
            'posts_custom_fields',
            'categories_columns',
            'categories_custom_fields',
            'tags_columns',
            'tags_custom_fields',
            'comments_columns',
            'comments_custom_fields',
        ];

        $state = [];
        foreach ($keys as $key) {
            $default = match ($key) {
                'navigation_group' => 'CMS',
                'navigation_categories_enabled' => true,
                'navigation_categories_label' => 'Categories',
                'navigation_tags_enabled' => true,
                'navigation_tags_label' => 'Tags',
                'posts_columns' => ['id', 'featured_image', 'title', 'status', 'published_at'],
                'posts_filters' => ['status', 'published_at'],
                'categories_columns' => ['id', 'name', 'parent', 'order'],
                'tags_columns' => ['id', 'name'],
                'comments_columns' => ['id', 'post', 'author', 'content', 'status', 'created_at'],
                default => [],
            };
            $state[$key] = CmsSetting::get($key, $default);
        }

        $this->getSchema('form')->fill($state);
    }

    public function formSchema(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Navigation')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                TextInput::make('navigation_group')
                                    ->label('Sidebar Navigation Group Name')
                                    ->required()
                                    ->default('CMS'),
                                Toggle::make('navigation_categories_enabled')
                                    ->label('Show Categories in Sidebar Menu')
                                    ->default(true),
                                TextInput::make('navigation_categories_label')
                                    ->label('Categories Sidebar Label')
                                    ->required()
                                    ->default('Categories'),
                                Toggle::make('navigation_tags_enabled')
                                    ->label('Show Tags in Sidebar Menu')
                                    ->default(true),
                                TextInput::make('navigation_tags_label')
                                    ->label('Tags Sidebar Label')
                                    ->required()
                                    ->default('Tags'),
                            ]),

                        Tab::make('Posts Configuration')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                CheckboxList::make('posts_columns')
                                    ->label('Visible Columns in Posts Table')
                                    ->options([
                                        'id' => 'ID',
                                        'serial_number' => 'Serial Number',
                                        'featured_image' => 'Featured Image',
                                        'title' => 'Title',
                                        'status' => 'Status',
                                        'published_at' => 'Published At',
                                    ])
                                    ->columns(2),
                                CheckboxList::make('posts_filters')
                                    ->label('Active Filters in Posts Table')
                                    ->options([
                                        'status' => 'Status Filter',
                                        'published_at' => 'Published Date Filter',
                                        'categories' => 'Categories Filter',
                                        'tags' => 'Tags Filter',
                                        'authors' => 'Authors Filter',
                                    ])
                                    ->columns(2),
                                Repeater::make('posts_custom_fields')
                                    ->label('Posts Dynamic Custom Fields')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Field Key (lowercase, alphanumeric, e.g. "subtitle")')
                                            ->required()
                                            ->rules(['regex:/^[a-z0-9_]+$/']),
                                        TextInput::make('label')
                                            ->label('Field Label')
                                            ->required(),
                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'textarea' => 'Textarea',
                                                'number' => 'Number',
                                                'toggle' => 'Toggle (Checkbox)',
                                                'select' => 'Select (Dropdown)',
                                            ])
                                            ->required()
                                            ->live(),
                                        TextInput::make('options')
                                            ->label('Dropdown Options (Comma separated, e.g. "Draft,Final")')
                                            ->placeholder('Option 1,Option 2')
                                            ->visible(fn ($get) => $get('type') === 'select')
                                            ->required(fn ($get) => $get('type') === 'select'),
                                        Toggle::make('required')
                                            ->label('Required Field'),
                                        Toggle::make('is_translatable')
                                            ->label('Translatable (Multi-lingual)'),
                                    ])
                                    ->collapsible()
                                    ->cloneable(),
                            ]),

                        Tab::make('Categories Configuration')
                            ->icon('heroicon-o-folder')
                            ->schema([
                                CheckboxList::make('categories_columns')
                                    ->label('Visible Columns in Categories Table')
                                    ->options([
                                        'id' => 'ID',
                                        'name' => 'Name',
                                        'parent' => 'Parent ID',
                                        'order' => 'Sort Order',
                                    ])
                                    ->columns(2),
                                Repeater::make('categories_custom_fields')
                                    ->label('Categories Dynamic Custom Fields')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Field Key')
                                            ->required()
                                            ->rules(['regex:/^[a-z0-9_]+$/']),
                                        TextInput::make('label')
                                            ->label('Field Label')
                                            ->required(),
                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'textarea' => 'Textarea',
                                                'number' => 'Number',
                                                'toggle' => 'Toggle (Checkbox)',
                                                'select' => 'Select (Dropdown)',
                                            ])
                                            ->required()
                                            ->live(),
                                        TextInput::make('options')
                                            ->label('Dropdown Options')
                                            ->visible(fn ($get) => $get('type') === 'select')
                                            ->required(fn ($get) => $get('type') === 'select'),
                                        Toggle::make('required')
                                            ->label('Required Field'),
                                        Toggle::make('is_translatable')
                                            ->label('Translatable (Multi-lingual)'),
                                    ]),
                            ]),

                        Tab::make('Tags Configuration')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                CheckboxList::make('tags_columns')
                                    ->label('Visible Columns in Tags Table')
                                    ->options([
                                        'id' => 'ID',
                                        'name' => 'Name',
                                    ])
                                    ->columns(2),
                                Repeater::make('tags_custom_fields')
                                    ->label('Tags Dynamic Custom Fields')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Field Key')
                                            ->required()
                                            ->rules(['regex:/^[a-z0-9_]+$/']),
                                        TextInput::make('label')
                                            ->label('Field Label')
                                            ->required(),
                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'textarea' => 'Textarea',
                                                'number' => 'Number',
                                                'toggle' => 'Toggle (Checkbox)',
                                                'select' => 'Select (Dropdown)',
                                            ])
                                            ->required()
                                            ->live(),
                                        TextInput::make('options')
                                            ->label('Dropdown Options')
                                            ->visible(fn ($get) => $get('type') === 'select')
                                            ->required(fn ($get) => $get('type') === 'select'),
                                        Toggle::make('required')
                                            ->label('Required Field'),
                                        Toggle::make('is_translatable')
                                            ->label('Translatable (Multi-lingual)'),
                                    ]),
                            ]),

                        Tab::make('Comments Configuration')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                CheckboxList::make('comments_columns')
                                    ->label('Visible Columns in Comments Table')
                                    ->options([
                                        'id' => 'ID',
                                        'post' => 'Post Title',
                                        'author' => 'Author Name/Email',
                                        'content' => 'Content Excerpt',
                                        'status' => 'Status',
                                        'created_at' => 'Created At',
                                    ])
                                    ->columns(2),
                                Repeater::make('comments_custom_fields')
                                    ->label('Comments Dynamic Custom Fields')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Field Key')
                                            ->required()
                                            ->rules(['regex:/^[a-z0-9_]+$/']),
                                        TextInput::make('label')
                                            ->label('Field Label')
                                            ->required(),
                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'textarea' => 'Textarea',
                                                'number' => 'Number',
                                                'toggle' => 'Toggle (Checkbox)',
                                                'select' => 'Select (Dropdown)',
                                            ])
                                            ->required()
                                            ->live(),
                                        TextInput::make('options')
                                            ->label('Dropdown Options')
                                            ->visible(fn ($get) => $get('type') === 'select')
                                            ->required(fn ($get) => $get('type') === 'select'),
                                        Toggle::make('required')
                                            ->label('Required Field'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->getSchema('form')->getState();

        foreach ($state as $key => $value) {
            CmsSetting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('CMS settings have been successfully updated and cached.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->color('success')
                ->submit('save'),
        ];
    }
}
