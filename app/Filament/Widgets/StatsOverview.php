<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran; // Jangan lupa import Model
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Setting biar widget ini refresh otomatis tiap 15 detik (Realtime-ish)
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Hitung Pemasukan HARI INI
        $pemasukanHariIni = Pembayaran::whereDate('created_at', now()->today())->sum('jumlah_bayar');

        // 2. Hitung Pemasukan BULAN INI
        $pemasukanBulanIni = Pembayaran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // 3. Total Siswa
        $totalSiswa = Siswa::count();

        return [
            // KARTU 1: PEMASUKAN HARI INI
            Stat::make('Pemasukan Hari Ini', 'Rp ' . number_format($pemasukanHariIni, 0, ',', '.'))
                ->description('Total uang masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success') // Warna Hijau
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik hiasan doang biar keren

            // KARTU 2: PEMASUKAN BULAN INI
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description('Akumulasi bulan ' . now()->format('F'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'), // Warna Biru

            // KARTU 3: TOTAL SISWA
            Stat::make('Total Siswa', $totalSiswa)
                ->description('Siswa terdaftar aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'), // Warna Kuning
        ];
    }
}