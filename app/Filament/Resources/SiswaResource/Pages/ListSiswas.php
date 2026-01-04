<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Kelas; // Import Model Kelas
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab; // Import Tab Component
use Illuminate\Database\Eloquent\Builder;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // === FITUR MAGIC TABS PER KELAS ===
    public function getTabs(): array
    {
        // 1. Tab Default "Semua Siswa"
        $tabs = [
            'all' => Tab::make('Semua Siswa'),
        ];

        // 2. Ambil semua data Kelas dari database
        $kelases = Kelas::all();

        // 3. Looping: Bikin Tab buat setiap kelas
        foreach ($kelases as $kelas) {
            // Nama Tab pake nama kelas (misal: XII RPL 1)
            // Slugnya pake ID kelas biar unik
            $tabs[$kelas->id] = Tab::make($kelas->nama_kelas)
                ->modifyQueryUsing(function (Builder $query) use ($kelas) {
                    // Logic Filternya:
                    // Tampilkan siswa yang id_kelas-nya sesuai tab yang diklik
                    return $query->where('id_kelas', $kelas->id);
                })
                ->badge(
                    // Opsional: Kasih angka jumlah siswa di sebelah nama kelas
                    \App\Models\Siswa::where('id_kelas', $kelas->id)->count()
                );
        }

        return $tabs;
    }
}