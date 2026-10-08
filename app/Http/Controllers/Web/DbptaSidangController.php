<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SidangSkripsi;
use App\Models\Siswa;
use App\Models\TugasAkhir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DbptaSidangController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa?->id ?? 1)->first();
        $sidangList = SidangSkripsi::where('id_tugas_akhir', $tugasAkhir?->id ?? 1)
            ->latest()
            ->get();

        return view('siakad.dbpta.sidang', compact('siswa', 'tugasAkhir', 'sidangList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();
        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa?->id ?? 1)->first();

        SidangSkripsi::create([
            'id_tugas_akhir' => $tugasAkhir?->id ?? 1,
            'jenis_sidang' => 'Sidang Akhir',
            'waktu_sidang' => now()->addDays(7),
            'nilai_rata_rata' => 88.50,
            'nilai_huruf' => 'A',
            'hasil_keputusan' => 'Lulus Tanpa Revisi',
        ]);

        ActivityLog::record('SIDANG_REGISTER', "Mahasiswa mendaftar Sidang Skripsi / Yudisium.");

        return redirect()->route('siakad.dbpta.sidang.index')->with('success', 'Pendaftaran Sidang Skripsi / Yudisium berhasil diajukan!');
    }
}
