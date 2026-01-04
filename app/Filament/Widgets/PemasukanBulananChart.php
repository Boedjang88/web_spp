<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PemasukanBulananChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pemasukan Tahun Ini';
    
    // Urutan Widget (biar ada di bawah kotak statistik tadi)
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        // Logika Mengambil Data Per Bulan (Jan - Des)
        $data = [];
        $bulan = [];
        
        for ($i = 1; $i <= 12; $i++) {
            // Ambil nama bulan (Jan, Feb, dst)
            $bulan[] = Carbon::create()->month($i)->format('M');
            
            // Hitung total bayar di bulan $i tahun ini
            $total = Pembayaran::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $i)
                ->sum('jumlah_bayar');
                
            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan SPP (Rp)',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB', // Warna batang biru
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $bulan, // Label bawah (Jan, Feb, Mar...)
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa ganti 'line' kalau mau garis
    }
}