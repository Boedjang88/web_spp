<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalSpp = Spp::count();
        $totalPemasukan = Pembayaran::sum('jumlah_bayar');

        $transaksiHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())->count();
        $pemasukanHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())->sum('jumlah_bayar');

        $transaksiBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->count();
            
        $pemasukanBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');

        $transaksiTerbaru = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->limit(7)
            ->get();

        // 12 Months Revenue Trends for Current Year
        $currentYear = now()->year;
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartData = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = (int) Pembayaran::whereMonth('tgl_bayar', $m)
                ->whereYear('tgl_bayar', $currentYear)
                ->sum('jumlah_bayar');
        }

        // Distribution of Students per Class
        $kelasLabels = [];
        $kelasData = [];
        $kelasList = Kelas::withCount('siswas')->get();
        foreach ($kelasList as $k) {
            $kelasLabels[] = $k->nama_kelas;
            $kelasData[] = $k->siswas_count;
        }

        return view('dashboard.index', compact(
            'totalSiswa',
            'totalKelas',
            'totalSpp',
            'totalPemasukan',
            'transaksiHariIni',
            'pemasukanHariIni',
            'transaksiBulanIni',
            'pemasukanBulanIni',
            'transaksiTerbaru',
            'chartLabels',
            'chartData',
            'kelasLabels',
            'kelasData',
            'currentYear'
        ));
    }
}
