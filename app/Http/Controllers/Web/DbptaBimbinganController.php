<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LogbookBimbingan;
use App\Models\Siswa;
use App\Models\TugasAkhir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DbptaBimbinganController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa?->id ?? 1)->first();
        $logbookList = LogbookBimbingan::where('id_siswa', $siswa?->id ?? 1)
            ->latest()
            ->get();

        return view('siakad.dbpta.bimbingan', compact('siswa', 'tugasAkhir', 'logbookList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bab' => 'required|string|max:50',
            'materi_bimbingan' => 'required|string|max:1000',
            'saran_dosen' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswaId)->first();
        $guru = \App\Models\Guru::first() ?? \App\Models\Guru::create([
            'nip' => '19800101',
            'nama_guru' => 'Dosen Pembimbing',
            'jenis_kelamin' => 'L',
        ]);
        $guruId = $guru->id;

        LogbookBimbingan::create([
            'id_tugas_akhir' => $tugasAkhir?->id ?? 1,
            'id_guru' => $guruId,
            'tanggal_bimbingan' => now(),
            'catatan_kemajuan_mahasiswa' => "[$validated[bab]] {$validated['materi_bimbingan']}",
            'arahan_dosen_pembimbing' => $validated['saran_dosen'] ?? 'Lanjutkan ke bab berikutnya & perbaiki daftar pustaka.',
            'status_acc' => 'Disetujui',
        ]);

        ActivityLog::record('LOGBOOK_BIMBINGAN', "Mahasiswa mencatat bimbingan TA: {$validated['bab']}");

        return redirect()->route('siakad.dbpta.bimbingan.index')->with('success', 'Catatan logbook bimbingan berhasil ditambahkan!');
    }
}
