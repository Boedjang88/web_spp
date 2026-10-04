<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Data Ringkasan Umum & Akademik
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalMapel = Mapel::count();
        $totalKelas = Kelas::count();
        $totalSpp = Spp::count();
        $totalPemasukan = Pembayaran::sum('jumlah_bayar');

        // 2. Data Transaksi SPP
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
            ->limit(6)
            ->get();

        // 3. Jadwal Mengajar Hari Ini
        $mapHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariIni = $mapHari[now()->format('l')] ?? 'Senin';
        $jadwalHariIni = JadwalPelajaran::with(['kelas', 'mapel', 'guru'])
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        // 4. Presensi Kehadiran Hari Ini
        $presensiHariIni = [
            'hadir' => Presensi::whereDate('tanggal', now()->toDateString())->where('status', 'Hadir')->count(),
            'izin' => Presensi::whereDate('tanggal', now()->toDateString())->where('status', 'Izin')->count(),
            'sakit' => Presensi::whereDate('tanggal', now()->toDateString())->where('status', 'Sakit')->count(),
            'alpa' => Presensi::whereDate('tanggal', now()->toDateString())->where('status', 'Alpa')->count(),
        ];

        // 5. Tren Pemasukan SPP Bulanan
        $currentYear = now()->year;
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartData = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = (int) Pembayaran::whereMonth('tgl_bayar', $m)
                ->whereYear('tgl_bayar', $currentYear)
                ->sum('jumlah_bayar');
        }

        // 6. Distribusi Siswa per Kelas
        $kelasLabels = [];
        $kelasData = [];
        $kelasList = Kelas::withCount('siswas')->get();
        foreach ($kelasList as $k) {
            $kelasLabels[] = $k->nama_kelas;
            $kelasData[] = $k->siswas_count;
        }

        return view('dashboard.index', compact(
            'totalSiswa',
            'totalGuru',
            'totalMapel',
            'totalKelas',
            'totalSpp',
            'totalPemasukan',
            'transaksiHariIni',
            'pemasukanHariIni',
            'transaksiBulanIni',
            'pemasukanBulanIni',
            'transaksiTerbaru',
            'hariIni',
            'jadwalHariIni',
            'presensiHariIni',
            'chartLabels',
            'chartData',
            'kelasLabels',
            'kelasData',
            'currentYear'
        ));
    }
}
