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
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        $tugasAkhir = $siswa ? TugasAkhir::where('id_siswa', $siswa->id)->first() : null;
        $logbookList = $siswa
            ? LogbookBimbingan::where('id_siswa', $siswa->id)->latest()->get()
            : collect();

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
        $siswa = $user?->siswa ?? $user?->mahasiswa;
        if (!$siswa) {
            return back()->with('error', 'Profil mahasiswa Anda belum terhubung. Tidak dapat mencatat logbook bimbingan.');
        }
        $siswaId = $siswa->id;

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswaId)->first();
        if (!$tugasAkhir) {
            return back()->with('error', 'Anda belum memiliki pendaftaran Skripsi/Tugas Akhir aktif. Silakan ajukan proposal terlebih dahulu.');
        }

        $guruId = $tugasAkhir->pembimbings()->first()?->id_guru
            ?? \App\Models\Guru::first()?->id
            ?? \App\Models\Guru::create([
                'nip' => '19800101',
                'nama_guru' => 'Dosen Pembimbing',
                'jenis_kelamin' => 'L',
            ])->id;

        LogbookBimbingan::create([
            'id_tugas_akhir' => $tugasAkhir->id,
            'id_guru' => $guruId,
            'tanggal_bimbingan' => now(),
            'catatan_kemajuan_mahasiswa' => "[$validated[bab]] {$validated['materi_bimbingan']}",
            'arahan_dosen_pembimbing' => $validated['saran_dosen'] ?? null,
            'status_acc' => 'Pending',
        ]);

        ActivityLog::record('LOGBOOK_BIMBINGAN', "Mahasiswa mencatat bimbingan TA: {$validated['bab']} (Menunggu Review Dosen)");

        return redirect()->route('siakad.dbpta.bimbingan.index')->with('success', 'Catatan logbook bimbingan berhasil ditambahkan dan menunggu paraf/review dosen pembimbing!');
    }

    public function approve(int $id): RedirectResponse
    {
        $logbook = LogbookBimbingan::findOrFail($id);
        $logbook->update([
            'status_acc' => 'Disetujui',
            'tgl_disetujui' => now(),
        ]);

        ActivityLog::record('LOGBOOK_APPROVE', "Dosen menyetujui logbook bimbingan #{$logbook->id}");

        return back()->with('success', 'Logbook bimbingan berhasil disetujui/di-paraf!');
    }
}
