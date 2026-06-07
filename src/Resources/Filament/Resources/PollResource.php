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
use Nepal360\FilamentCmsPro\Models\Poll;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Interactive';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Poll Details')
                    ->schema([
                        TextInput::make('question')
                            ->required()
                            ->maxLength(500),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'closed' => 'Closed',
                            ])
                            ->default('active')
                            ->required(),
                    ]),

                Section::make('Options')
                    ->schema([
                        Repeater::make('options')
                            ->relationship('options')
                            ->schema([
                                TextInput::make('option_text')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('votes_count')
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),
                            ])
                            ->minItems(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('question')
                    ->searchable()
                    ->limit(100),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'closed',
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
