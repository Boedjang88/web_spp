<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BapPerkuliahan;
use App\Models\Guru;
use App\Models\KelasKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenBapController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $guru = $user->guru ?? Guru::first();

        $kelasKuliahs = KelasKuliah::with('mataKuliah')
            ->when($guru && !$user->isAdmin(), function ($q) use ($guru) {
                $q->where('id_dosen', $guru->id);
            })
            ->get();

        $selectedKelasId = $request->get('id_kelas_kuliah', $kelasKuliahs->first()?->id);

        $bapList = BapPerkuliahan::with(['kelasKuliah.mataKuliah', 'dosen', 'ruangan'])
            ->when($selectedKelasId, function ($q) use ($selectedKelasId) {
                $q->where('id_kelas_kuliah', $selectedKelasId);
            })
            ->orderBy('pertemuan_ke')
            ->get();

        return view('siakad.dosen.bap-index', compact('guru', 'kelasKuliahs', 'selectedKelasId', 'bapList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kelas_kuliah' => 'required|exists:kelas_kuliahs,id',
            'pertemuan_ke' => 'required|integer|min:1|max:16',
            'tanggal_pelaksanaan' => 'required|date',
            'jam_mulai_real' => 'required',
            'jam_selesai_real' => 'required',
            'materi_pembahasan' => 'required|string|max:255',
            'catatan_dosen' => 'nullable|string',
            'total_mahasiswa_hadir' => 'required|integer|min:0',
        ]);

        $user = auth()->user();
        $guru = $user->guru ?? Guru::first();

        $signatureHash = hash('sha256', 'BAP-' . $validated['id_kelas_kuliah'] . '-P' . $validated['pertemuan_ke'] . '-' . now()->timestamp);

        BapPerkuliahan::updateOrCreate(
            [
                'id_kelas_kuliah' => $validated['id_kelas_kuliah'],
                'pertemuan_ke' => $validated['pertemuan_ke'],
            ],
            [
                'id_guru' => $guru->id,
                'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
                'jam_mulai_real' => $validated['jam_mulai_real'],
                'jam_selesai_real' => $validated['jam_selesai_real'],
                'materi_pembahasan' => $validated['materi_pembahasan'],
                'catatan_dosen' => $validated['catatan_dosen'],
                'total_mahasiswa_hadir' => $validated['total_mahasiswa_hadir'],
                'status_verifikasi' => 'Tersubmit',
                'digital_signature_hash' => $signatureHash,
            ]
        );

        ActivityLog::record('BAP_CREATE', "Dosen mengisi Berita Acara Perkuliahan Pertemuan #{$validated['pertemuan_ke']}");

        return redirect()->route('siakad.dosen.bap.index', ['id_kelas_kuliah' => $validated['id_kelas_kuliah']])
            ->with('success', 'Berita Acara Perkuliahan (BAP) & Digital Signature berhasil disimpan!');
    }
}
