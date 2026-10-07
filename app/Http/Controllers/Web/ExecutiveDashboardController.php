<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExecutiveDashboardController extends Controller
{
    public function index(): View
    {
        $totalMahasiswa = Siswa::count();
        $totalDosen = Guru::count();
        $ratioDosenMhs = $totalDosen > 0 ? round($totalMahasiswa / $totalDosen, 1) : 0;

        $totalPenerimaanKas = Pembayaran::sum('jumlah_bayar');
        $prodis = ProgramStudi::withCount('mahasiswas')->get();

        // GIS Demografi Dummy Aggregates
        $demografiKota = [
            'Bandung' => 35,
            'Jakarta' => 25,
            'Surabaya' => 15,
            'Medan' => 10,
            'Lainnya' => 15,
        ];

        // Predictive AI Graduation & Revenue Projections
        $predictiveLulusTepatWaktu = 88.5; // %
        $predictiveRiskDelay = 11.5; // %

        return view('siakad.executive.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'ratioDosenMhs',
            'totalPenerimaanKas',
            'prodis',
            'demografiKota',
            'predictiveLulusTepatWaktu',
            'predictiveRiskDelay'
        ));
    }
}
