<?php

namespace App\Filament\Parent\Resources\TagihanUktResource\Pages;

use App\Filament\Parent\Resources\TagihanUktResource;
use Filament\Resources\Pages\ListRecords;

class ListTagihanUkts extends ListRecords
{
    protected static string $resource = TagihanUktResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
