<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Kelas; // Import Model Kelas
use App\Models\Pembayaran; // Import Model Pembayaran
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab; // Import Tab
use Illuminate\Database\Eloquent\Builder;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // === FITUR TABS PER KELAS DI MENU PEMBAYARAN ===
    public function getTabs(): array
    {
        // 1. Tab Default "Semua Transaksi"
        $tabs = [
            'all' => Tab::make('Semua Transaksi'),
        ];

        // 2. Ambil data Kelas
        $kelases = Kelas::all();

        foreach ($kelases as $kelas) {
            $tabs[$kelas->id] = Tab::make($kelas->nama_kelas)
                ->modifyQueryUsing(function (Builder $query) use ($kelas) {
                    // LOGIC PENTING:
                    // Cari pembayaran dimana 'siswa'-nya punya 'id_kelas' yang sesuai
                    return $query->whereHas('siswa', function ($q) use ($kelas) {
                        $q->where('id_kelas', $kelas->id);
                    });
                })
                ->badge(
                    // Opsional: Hitung berapa kali transaksi terjadi di kelas ini
                    Pembayaran::whereHas('siswa', function ($q) use ($kelas) {
                        $q->where('id_kelas', $kelas->id);
                    })->count()
                );
        }

        return $tabs;
    }
}