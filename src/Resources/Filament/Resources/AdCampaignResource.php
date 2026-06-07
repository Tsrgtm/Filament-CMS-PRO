<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Nepal360\FilamentCmsPro\Models\AdCampaign;

class AdCampaignResource extends Resource
{
    protected static ?string $model = AdCampaign::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Campaign Setup')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('placement')
                            ->options([
                                'header' => 'Header Banner',
                                'sidebar' => 'Sidebar Widget',
                                'inline_before' => 'Inline (Before Article)',
                                'inline_after' => 'Inline (After Article)',
                                'footer' => 'Footer Banner',
                            ])
                            ->required(),
                        Textarea::make('ad_code')
                            ->required()
                            ->rows(5)
                            ->placeholder('Paste HTML, Google AdSense script, or raw iframe code here.'),
                    ]),

                Section::make('Campaign Scheduling')
                    ->schema([
                        DateTimePicker::make('starts_at'),
                        DateTimePicker::make('ends_at'),
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
                TextColumn::make('name')->searchable(),
                TextColumn::make('placement')->badge(),
                IconColumn::make('is_active')->boolean()->sortable(),
                TextColumn::make('starts_at')->dateTime()->sortable(),
                TextColumn::make('ends_at')->dateTime()->sortable(),
            ]);
    }
}
