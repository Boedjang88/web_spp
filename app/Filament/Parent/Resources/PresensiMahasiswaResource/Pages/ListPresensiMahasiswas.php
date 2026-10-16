<?php

namespace App\Filament\Parent\Resources\PresensiMahasiswaResource\Pages;

use App\Filament\Parent\Resources\PresensiMahasiswaResource;
use Filament\Resources\Pages\ListRecords;

class ListPresensiMahasiswas extends ListRecords
{
    protected static string $resource = PresensiMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
