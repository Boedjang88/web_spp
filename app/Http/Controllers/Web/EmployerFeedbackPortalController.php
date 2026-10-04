<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmployerFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerFeedbackPortalController extends Controller
{
    /**
     * Display Guest-Accessible Token Secured Employer Feedback Form
     */
    public function show(string $token): View
    {
        $feedback = EmployerFeedback::with('tracerStudy.mahasiswa')
            ->where('access_token', $token)
            ->firstOrFail();

        return view('siakad.employer.survey', compact('feedback'));
    }

    /**
     * Submit Employer Feedback
     */
    public function update(Request $request, string $token): RedirectResponse
    {
        $feedback = EmployerFeedback::where('access_token', $token)->firstOrFail();

        $validated = $request->validate([
            'nama_penilai_atasan' => 'required|string|max:150',
            'jabatan_penilai' => 'required|string|max:100',
            'email_perusahaan' => 'required|email|max:150',
            'nama_perusahaan' => 'required|string|max:150',
            'skor_integritas_etika' => 'required|integer|min:1|max:5',
            'skor_keahlian_bidang' => 'required|integer|min:1|max:5',
            'skor_bahasa_asing' => 'required|integer|min:1|max:5',
            'skor_penggunaan_ti' => 'required|integer|min:1|max:5',
            'skor_komunikasi' => 'required|integer|min:1|max:5',
            'skor_kerjasama_tim' => 'required|integer|min:1|max:5',
            'skor_pengembangan_diri' => 'required|integer|min:1|max:5',
            'saran_kurikulum' => 'nullable|string',
        ]);

        $feedback->update(array_merge($validated, [
            'is_completed' => true,
            'completed_at' => now(),
        ]));

        return back()->with('success', 'Terima kasih! Kuesioner evaluasi kinerja alumni telah berhasil disimpan.');
    }
}
