<?php

namespace App\Filament\Parent\Resources\NilaiObeResource\Pages;

use App\Filament\Parent\Resources\NilaiObeResource;
use Filament\Resources\Pages\ListRecords;

class ListNilaiObes extends ListRecords
{
    protected static string $resource = NilaiObeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
