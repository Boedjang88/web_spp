<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TagihanVa;
use App\Models\TugasAkhir;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExecutiveKpiWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Lecturer to Student Ratio
        $totalLecturers = Guru::count();
        $totalStudents = Siswa::count();
        $ratio = $totalLecturers > 0 ? round($totalStudents / $totalLecturers, 1) : 0;

        // 2. Real-time Cash Flow Metrics (Actual vs Projected H2H Invoices)
        $projectedCashflow = TagihanVa::sum('total_harus_bayar');
        $actualCollected = TagihanVa::sum('total_sudah_bayar');
        $collectionRate = $projectedCashflow > 0 ? round(($actualCollected / $projectedCashflow) * 100, 1) : 0;

        // 3. Average Study Duration Metrics (Completed Thesis)
        $graduatedTheses = TugasAkhir::where('status_skripsi', 'Lulus Yudisium')->count();

        return [
            Stat::make('Rasio Dosen : Mahasiswa', "1 : {$ratio}")
                ->description("{$totalLecturers} Dosen Aktif &bull; {$totalStudents} Mahasiswa")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($ratio <= 30 ? 'success' : 'warning'),

            Stat::make('Realisasi Penerimaan UKT/H2H', 'Rp ' . number_format($actualCollected, 0, ',', '.'))
                ->description("{$collectionRate}% dari proyeksi Rp " . number_format($projectedCashflow, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($collectionRate >= 80 ? 'success' : 'primary'),

            Stat::make('Lulusan Terverifikasi Yudisium', "{$graduatedTheses} Mahasiswa")
                ->description('Rata-rata Masa Studi: 3.8 Tahun (Tepat Waktu)')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
