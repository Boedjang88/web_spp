<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CourseMaterial;
use App\Models\Krs;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Services\Skpi\SkpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureDocumentDownloadController extends Controller
{
    /**
     * Download signed payment receipt
     */
    public function downloadReceipt(Request $request, $id)
    {
        return app(\App\Http\Controllers\Web\PembayaranController::class)->cetakKwitansi($id);
    }

    /**
     * Download signed exam pass / kartu ujian
     */
    public function downloadExamPass(Request $request, $id_krs)
    {
        $krs = Krs::with(['mahasiswa.kelas', 'tahunAkademik', 'details.kelasKuliah.mataKuliah'])->findOrFail($id_krs);
        $siswa = $krs->mahasiswa;
        return view('web.siswa.kartu-ujian', compact('siswa', 'krs'));
    }

    /**
     * Download signed KHS / E-Rapor
     */
    public function downloadRapor(Request $request, $id_siswa, $id_tahun_akademik)
    {
        $siswa = Siswa::with(['kelas', 'nilais.mapel'])->findOrFail($id_siswa);
        $nilais = $siswa->nilais;
        return view('web.nilai.rapor', compact('siswa', 'nilais'));
    }

    /**
     * Download signed SKPI Certificate
     */
    public function downloadSkpi(Request $request, $id_siswa)
    {
        $service = app(SkpiService::class);
        $skpi = $service->generateBilingualSupplement($id_siswa);
        return view('siakad.skpi.print', compact('skpi'));
    }

    /**
     * Download LMS material file
     */
    public function downloadLmsMaterial(Request $request, $id)
    {
        $material = CourseMaterial::findOrFail($id);

        if (!$material->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Materi perkuliahan belum dirilis (Timed Release Lock).',
            ], 403);
        }

        if (Storage::disk('local')->exists($material->file_path)) {
            return Storage::disk('local')->download($material->file_path, $material->original_filename);
        }

        if (file_exists($material->file_path)) {
            return response()->download($material->file_path, $material->original_filename);
        }

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diakses (Streamed).',
            'material' => $material,
        ]);
    }
}
