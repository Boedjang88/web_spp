<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Dosen;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Mahasiswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Map English Day to Indonesian
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

        // 1. Data Khusus Siswa / Mahasiswa
        if ($user && $user->isMahasiswa()) {
            $siswa = $user->siswa ?? Siswa::with(['kelas', 'spp'])->first();
            $mahasiswa = $user->mahasiswa ?? Mahasiswa::first();

            $jadwalSiswa = collect();
            $nilaiSiswa = collect();
            $presensiSummary = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'persentase' => 100];
            $tunggakan = ['total_bulan' => 0, 'total_rupiah' => 0, 'list_bulan' => []];
            $riwayatPembayaran = collect();

            if ($siswa) {
                $jadwalSiswa = JadwalPelajaran::with(['mapel', 'guru'])
                    ->where('id_kelas', $siswa->id_kelas)
                    ->where('hari', $hariIni)
                    ->orderBy('jam_mulai')
                    ->get();

                $nilaiSiswa = Nilai::with(['mapel', 'guru'])
                    ->where('id_siswa', $siswa->id)
                    ->latest()
                    ->take(5)
                    ->get();

                $presensiAll = Presensi::where('id_siswa', $siswa->id)->get();
                $totalPresensi = $presensiAll->count();
                if ($totalPresensi > 0) {
                    $hadir = $presensiAll->where('status', 'Hadir')->count();
                    $izin = $presensiAll->where('status', 'Izin')->count();
                    $sakit = $presensiAll->where('status', 'Sakit')->count();
                    $alpa = $presensiAll->where('status', 'Alpa')->count();
                    $persentase = round(($hadir / $totalPresensi) * 100, 1);
                    $presensiSummary = compact('hadir', 'izin', 'sakit', 'alpa', 'persentase');
                }

                $tunggakan = $siswa->info_tunggakan;
                $riwayatPembayaran = Pembayaran::with(['spp'])
                    ->where('id_siswa', $siswa->id)
                    ->latest('tgl_bayar')
                    ->take(5)
                    ->get();
            }

            $viewName = view()->exists('dashboard.mahasiswa') ? 'dashboard.mahasiswa' : 'dashboard.siswa';
            return view($viewName, compact('siswa', 'jadwalSiswa', 'nilaiSiswa', 'presensiSummary', 'tunggakan', 'riwayatPembayaran', 'hariIni'));
        }

        // 2. Data Khusus Dosen / Guru
        if ($user && $user->isDosen()) {
            $guru = $user->guru ?? Guru::first();

            $jadwalGuruHariIni = collect();
            $totalJadwalAjar = 0;
            $nilaiTerbaruGuru = collect();

            if ($guru) {
                $jadwalGuruHariIni = JadwalPelajaran::with(['kelas', 'mapel'])
                    ->where('id_guru', $guru->id)
                    ->where('hari', $hariIni)
                    ->orderBy('jam_mulai')
                    ->get();

                $totalJadwalAjar = JadwalPelajaran::where('id_guru', $guru->id)->count();

                $nilaiTerbaruGuru = Nilai::with(['siswa.kelas', 'mapel'])
                    ->where('id_guru', $guru->id)
                    ->latest()
                    ->take(6)
                    ->get();
            }

            $viewName = view()->exists('dashboard.dosen') ? 'dashboard.dosen' : 'dashboard.guru';
            return view($viewName, compact('guru', 'jadwalGuruHariIni', 'totalJadwalAjar', 'nilaiTerbaruGuru', 'hariIni'));
        }

        // 3. Data Umum / Superadmin, Admin TU & BAAK (Cached for 15 minutes / 900s)
        $cacheKey = 'dashboard_admin_aggregates_' . now()->toDateString();
        $aggregates = \Illuminate\Support\Facades\Cache::remember($cacheKey, 900, function () {
            $currentYear = now()->year;
            $chartData = [];
            for ($m = 1; $m <= 12; $m++) {
                $chartData[] = (int) Pembayaran::whereMonth('tgl_bayar', $m)->whereYear('tgl_bayar', $currentYear)->sum('jumlah_bayar');
            }

            $kelasList = Kelas::withCount('siswas')->get();

            return [
                'totalSiswa' => User::whereIn('role', ['mahasiswa', 'siswa', 'student'])->count(),
                'totalGuru' => User::whereIn('role', ['dosen', 'guru', 'lecturer'])->count(),
                'totalMapel' => Mapel::count(),
                'totalKelas' => Kelas::count(),
                'totalUsers' => User::count(),
                'totalPemasukan' => (float) (Pembayaran::sum('jumlah_bayar') ?? 0),
                'chartData' => $chartData,
                'kelasLabels' => $kelasList->pluck('nama_kelas')->toArray(),
                'kelasData' => $kelasList->pluck('siswas_count')->toArray(),
            ];
        });

        $totalSiswa = $aggregates['totalSiswa'];
        $totalGuru = $aggregates['totalGuru'];
        $totalMapel = $aggregates['totalMapel'];
        $totalKelas = $aggregates['totalKelas'];
        $totalUsers = $aggregates['totalUsers'];
        $totalPemasukan = $aggregates['totalPemasukan'];
        $chartData = $aggregates['chartData'];
        $kelasLabels = $aggregates['kelasLabels'];
        $kelasData = $aggregates['kelasData'];

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $currentYear = now()->year;

        $transaksiHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())->count();
        $pemasukanHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())->sum('jumlah_bayar') ?? 0;

        $transaksiBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->count();
            
        $pemasukanBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar') ?? 0;

        $transaksiTerbaru = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $jadwalHariIni = JadwalPelajaran::with(['kelas', 'mapel', 'guru'])
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->limit(6)
            ->get();

        $nilaiTerbaru = Nilai::with(['siswa.kelas', 'mapel', 'guru'])
            ->latest()
            ->limit(6)
            ->get();

        $presensisToday = Presensi::whereDate('tanggal', now()->toDateString())->get();
        $presensiHariIni = [
            'hadir' => $presensisToday->where('status', 'Hadir')->count(),
            'izin' => $presensisToday->where('status', 'Izin')->count(),
            'sakit' => $presensisToday->where('status', 'Sakit')->count(),
            'alpa' => $presensisToday->where('status', 'Alpa')->count(),
        ];

        return view('dashboard.index', compact(
            'totalSiswa',
            'totalGuru',
            'totalMapel',
            'totalKelas',
            'totalUsers',
            'totalPemasukan',
            'transaksiHariIni',
            'pemasukanHariIni',
            'transaksiBulanIni',
            'pemasukanBulanIni',
            'transaksiTerbaru',
            'jadwalHariIni',
            'nilaiTerbaru',
            'presensiHariIni',
            'hariIni',
            'currentYear',
            'chartLabels',
            'chartData',
            'kelasLabels',
            'kelasData'
        ));
    }
}
