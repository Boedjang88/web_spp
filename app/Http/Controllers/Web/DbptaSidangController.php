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
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        $tugasAkhir = $siswa ? TugasAkhir::where('id_siswa', $siswa->id)->first() : null;
        $sidangList = $tugasAkhir
            ? SidangSkripsi::where('id_tugas_akhir', $tugasAkhir->id)->latest()->get()
            : collect();

        return view('siakad.dbpta.sidang', compact('siswa', 'tugasAkhir', 'sidangList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;
        if (!$siswa) {
            return back()->with('error', 'Profil mahasiswa Anda belum terhubung. Tidak dapat mendaftar sidang.');
        }

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa->id)->first();
        if (!$tugasAkhir) {
            return back()->with('error', 'Data proposal tugas akhir belum terdaftar.');
        }

        SidangSkripsi::create([
            'id_tugas_akhir' => $tugasAkhir->id,
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
