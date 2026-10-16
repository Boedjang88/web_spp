<?php

namespace App\Filament\Widgets;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Hitung Pemasukan HARI INI
        $pemasukanHariIni = Pembayaran::whereDate('created_at', now()->today())->sum('jumlah_bayar');
        $transaksiHariIni = Pembayaran::whereDate('created_at', now()->today())->count();

        // 2. Hitung Pemasukan BULAN INI
        $pemasukanBulanIni = Pembayaran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // 3. Total Siswa & Kelas
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();

        // 4. Mahasiswa Lunas
        $siswaLunasCount = Siswa::all()->filter(fn ($s) => ($s->info_tunggakan['total_bulan'] ?? 0) === 0)->count();
        $persentaseLunas = $totalSiswa > 0 ? round(($siswaLunasCount / $totalSiswa) * 100, 1) : 100;

        return [
            // KARTU 1: TOTAL MAHASISWA AKTIF (F-Pattern anchor #1)
            Stat::make('Mahasiswa Aktif', number_format($totalSiswa, 0, ',', '.'))
                ->description("Tersebar di {$totalKelas} Rombel / Kelas")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([3, 5, 8, 12, 15, 20, 25]),

            // KARTU 2: PEMASUKAN BULAN INI (F-Pattern anchor #2)
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description('Periode ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([10, 25, 30, 45, 60, 75, 90]),

            // KARTU 3: PEMASUKAN HARI INI
            Stat::make('Pemasukan Hari Ini', 'Rp ' . number_format($pemasukanHariIni, 0, ',', '.'))
                ->description("{$transaksiHariIni} Transaksi terverifikasi")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([4, 8, 3, 12, 7, 16, 20]),

            // KARTU 4: PERSENTASE KELUNASAN UKT
            Stat::make('Tingkat Kelunasan UKT', "{$persentaseLunas}%")
                ->description("{$siswaLunasCount} dari {$totalSiswa} Mahasiswa Lunas")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($persentaseLunas >= 80 ? 'success' : 'warning')
                ->chart([50, 55, 62, 70, 78, 85, (int) $persentaseLunas]),
        ];
    }
}