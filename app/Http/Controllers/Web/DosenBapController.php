<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BapPerkuliahan;
use App\Models\Dosen;
use App\Models\Guru;
use App\Models\KelasKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenBapController extends Controller
{
    protected function resolveFacultyUser()
    {
        $user = auth()->user();
        $guru = $user?->guru ?? ($user?->id_guru ? Guru::find($user->id_guru) : null);
        $dosen = $user?->dosen ?? ($user?->id_dosen ? Dosen::find($user->id_dosen) : null);

        return [$user, $guru, $dosen];
    }

    public function index(Request $request): View
    {
        [$user, $guru, $dosen] = $this->resolveFacultyUser();

        if (!$user->isAdmin() && !$user->isSuperAdmin() && !$guru && !$dosen) {
            abort(403, 'Akun Anda tidak memiliki peran Dosen Pengampu.');
        }

        $guruId = $guru?->id;
        $dosenId = $dosen?->id;

        $kelasKuliahs = KelasKuliah::with('mataKuliah')
            ->when(!$user->isAdmin() && !$user->isSuperAdmin(), function ($q) use ($guruId, $dosenId) {
                $q->where(function ($sub) use ($guruId, $dosenId) {
                    if ($dosenId) {
                        $sub->where('id_dosen', $dosenId);
                    }
                    if ($guruId) {
                        $sub->orWhere('id_guru', $guruId);
                    }
                });
            })
            ->get();

        $selectedKelasId = $request->get('id_kelas_kuliah', $kelasKuliahs->first()?->id);

        $bapList = BapPerkuliahan::with(['kelasKuliah.mataKuliah', 'dosen', 'ruangan'])
            ->when($selectedKelasId, function ($q) use ($selectedKelasId) {
                $q->where('id_kelas_kuliah', $selectedKelasId);
            })
            ->orderBy('pertemuan_ke')
            ->get();

        return view('siakad.dosen.bap-index', compact('guru', 'dosen', 'kelasKuliahs', 'selectedKelasId', 'bapList'));
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

        [$user, $guru, $dosen] = $this->resolveFacultyUser();

        $kelas = KelasKuliah::findOrFail($validated['id_kelas_kuliah']);

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $isAuthorized = ($dosen && $kelas->id_dosen == $dosen->id)
                || ($guru && $kelas->id_guru == $guru->id);

            abort_if(!$isAuthorized, 403, 'Anda bukan dosen pengampu untuk kelas perkuliahan ini.');
        }

        $lecturerId = $guru?->id ?? ($dosen?->id ?? 1);
        $signatureHash = hash('sha256', 'BAP-' . $validated['id_kelas_kuliah'] . '-P' . $validated['pertemuan_ke'] . '-' . now()->timestamp);

        BapPerkuliahan::updateOrCreate(
            [
                'id_kelas_kuliah' => $validated['id_kelas_kuliah'],
                'pertemuan_ke' => $validated['pertemuan_ke'],
            ],
            [
                'id_guru' => $lecturerId,
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

    public function finalizeCourse(Request $request, int $id): RedirectResponse
    {
        [$user, $guru, $dosen] = $this->resolveFacultyUser();

        $kelas = KelasKuliah::with('mataKuliah')->findOrFail($id);

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $isAuthorized = ($dosen && $kelas->id_dosen == $dosen->id)
                || ($guru && $kelas->id_guru == $guru->id);

            abort_if(!$isAuthorized, 403, 'Anda bukan dosen pengampu untuk kelas perkuliahan ini.');
        }

        $completedMeetings = BapPerkuliahan::where('id_kelas_kuliah', $id)
            ->whereIn('status_verifikasi', ['Tersubmit', 'Diverifikasi BAAK'])
            ->count();

        if ($completedMeetings < 16) {
            return redirect()->route('siakad.dosen.bap.index', ['id_kelas_kuliah' => $id])
                ->with('error', "Gagal menutup perkuliahan! Sesuai standar SN-Dikti, kelas harus menyelesaikan minimal 16 pertemuan (termasuk UTS & UAS) sebelum dapat difinalisasi. Saat ini baru terlaksana {$completedMeetings} pertemuan.");
        }

        BapPerkuliahan::where('id_kelas_kuliah', $id)->update([
            'status_verifikasi' => 'Diverifikasi BAAK',
        ]);

        ActivityLog::record('BAP_FINALIZE', "Dosen memfinalisasi 16 pertemuan perkuliahan kelas {$kelas->nama_kelas} ({$kelas->mataKuliah?->nama_mk}).");

        return redirect()->route('siakad.dosen.bap.index', ['id_kelas_kuliah' => $id])
            ->with('success', "Perkuliahan kelas {$kelas->nama_kelas} (16 pertemuan) berhasil difinalisasi! Pengisian nilai akhir mahasiswa telah dibuka.");
    }
}
