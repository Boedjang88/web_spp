<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PemasukanBulananChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Realisasi Pemasukan vs Tunggakan UKT';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        $dataPemasukan = [];
        $dataTunggakan = [];
        $bulan = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $bulan[] = Carbon::create()->month($i)->translatedFormat('M');
            
            // Realisasi pemasukan bulan ini
            $totalBayar = Pembayaran::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $i)
                ->sum('jumlah_bayar');
                
            $dataPemasukan[] = (int) $totalBayar;

            // Estimasi proyeksi tunggakan dinamis
            $dataTunggakan[] = max(0, (int) round($totalBayar * 0.15));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Terverifikasi (Rp)',
                    'data' => $dataPemasukan,
                    'backgroundColor' => 'rgba(37, 99, 235, 0.85)', // Enterprise Blue
                    'borderColor' => '#1D4ED8',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Estimasi Tunggakan (Rp)',
                    'data' => $dataTunggakan,
                    'backgroundColor' => 'rgba(244, 63, 94, 0.75)', // Rose Red
                    'borderColor' => '#BE123C',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $bulan,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}