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
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? \App\Models\Siswa::first();

        $suratList = SuratAkademik::when($siswa, function ($q) use ($siswa) {
                return $q->where('id_siswa', $siswa->id);
            })
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
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? \App\Models\Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        $nextId = (int) (SuratAkademik::max('id') ?? 0) + 1;
        $nomorSurat = 'SKMA/' . date('Y/m/') . sprintf('%04d', $nextId);

        while (SuratAkademik::where('nomor_surat', $nomorSurat)->exists()) {
            $nextId++;
            $nomorSurat = 'SKMA/' . date('Y/m/') . sprintf('%04d', $nextId);
        }

        $qrToken = hash('sha256', $siswaId . 'SURAT-' . $nextId . '-' . microtime(true) . '-TOKEN');

        SuratAkademik::create([
            'id_siswa' => $siswaId,
            'jenis_surat' => $validated['jenis_surat'],
            'nomor_surat' => $nomorSurat,
            'perihal' => $validated['perihal'],
            'keperluan' => $validated['keperluan'],
            'qr_verification_token' => $qrToken,
            'file_pdf_path' => 'documents/surat_aktif/skma_' . $siswaId . '.pdf',
            'status' => 'DRAFT',
            'tgl_terbit' => now(),
        ]);

        ActivityLog::record('ESURAT_REQUEST', "Mahasiswa mengajukan e-Surat ({$validated['jenis_surat']}) - Menunggu Persetujuan Admin/BAAK.");

        return redirect()->route('siakad.esurat.index')->with('success', "Permohonan e-Surat berhasil diajukan! Nomor Surat: {$nomorSurat} (Status: Menunggu Persetujuan Admin/BAAK)");
    }
}
