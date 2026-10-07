<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SuratAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentEsuratController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $suratList = SuratAkademik::where('id_siswa', $siswa?->id ?? 1)
            ->latest()
            ->get();

        return view('siakad.student.esurat-index', compact('siswa', 'suratList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_surat' => 'required|string|max:100',
            'perihal' => 'required|string|max:150',
            'keperluan' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $siswa = $user->siswa;
        $nextId = SuratAkademik::count() + 1;
        $nomorSurat = 'SKMA/2026/03/' . sprintf('%04d', $nextId);
        $qrToken = hash('sha256', ($siswa?->id ?? 1) . 'SURAT-' . $nextId . '-TOKEN');

        SuratAkademik::create([
            'id_siswa' => $siswa?->id ?? 1,
            'jenis_surat' => $validated['jenis_surat'],
            'nomor_surat' => $nomorSurat,
            'perihal' => $validated['perihal'],
            'keperluan' => $validated['keperluan'],
            'qr_verification_token' => $qrToken,
            'file_pdf_path' => 'documents/surat_aktif/skma_' . ($siswa?->id ?? 1) . '.pdf',
            'status' => 'DISETUJUI',
            'tgl_terbit' => now(),
        ]);

        ActivityLog::record('ESURAT_REQUEST', "Mahasiswa mengajukan e-Surat: {$validated['jenis_surat']}");

        return redirect()->route('siakad.esurat.index')->with('success', "Permohonan e-Surat Akademik berhasil diterbitkan! Nomor Surat: {$nomorSurat}");
    }
}
