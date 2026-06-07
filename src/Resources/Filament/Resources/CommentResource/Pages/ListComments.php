<?php

namespace Nepal360\FilamentCmsPro\Resources\Filament\Resources\CommentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Nepal360\FilamentCmsPro\Resources\Filament\Resources\CommentResource;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
