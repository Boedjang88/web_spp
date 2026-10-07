<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EdomEvaluasi;
use App\Models\EdomEvaluasiItem;
use App\Models\EdomPertanyaan;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentEdomController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user->siswa;
        $activeTa = TahunAkademik::where('is_active', true)->first();

        $krs = Krs::with(['details.kelasKuliah.mataKuliah', 'details.kelasKuliah.dosen'])
            ->where('id_siswa', $siswa?->id ?? 1)
            ->where('id_tahun_akademik', $activeTa?->id ?? 1)
            ->first();

        $pertanyaans = EdomPertanyaan::where('is_active', true)->orderBy('urutan')->get();

        // Get existing EDOM submissions by this student
        $existingEdoms = EdomEvaluasi::where('id_siswa', $siswa?->id ?? 1)->pluck('id_krs_detail')->toArray();

        return view('siakad.student.edom-index', compact('siswa', 'krs', 'pertanyaans', 'existingEdoms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_krs_detail' => 'required|exists:krs_details,id',
            'id_guru' => 'required|exists:gurus,id',
            'kritik_saran' => 'nullable|string|max:500',
            'skor' => 'required|array',
            'skor.*' => 'required|integer|min:1|max:5',
        ]);

        $user = auth()->user();
        $siswa = $user->siswa;

        $averageScore = array_sum($validated['skor']) / count($validated['skor']);

        $edom = EdomEvaluasi::updateOrCreate(
            [
                'id_krs_detail' => $validated['id_krs_detail'],
                'id_guru' => $validated['id_guru'],
            ],
            [
                'id_siswa' => $siswa?->id ?? 1,
                'skor_rata_rata' => round($averageScore, 2),
                'kritik_saran' => $validated['kritik_saran'],
            ]
        );

        foreach ($validated['skor'] as $pertanyaanId => $scoreValue) {
            EdomEvaluasiItem::updateOrCreate(
                [
                    'id_edom_evaluasi' => $edom->id,
                    'id_edom_pertanyaan' => $pertanyaanId,
                ],
                [
                    'skor_nilai' => $scoreValue,
                ]
            );
        }

        ActivityLog::record('EDOM_SUBMIT', "Mahasiswa mengisi Evaluasi Dosen (EDOM) untuk KRS Detail #{$validated['id_krs_detail']}");

        return redirect()->route('siakad.edom.index')->with('success', 'Evaluasi Dosen (EDOM) berhasil dikirim!');
    }
}
