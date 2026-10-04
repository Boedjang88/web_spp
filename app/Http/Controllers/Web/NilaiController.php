<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Nilai::with(['siswa.kelas', 'mapel', 'guru']);

        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }

        if ($request->filled('id_mapel')) {
            $query->where('id_mapel', $request->id_mapel);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $nilais = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();

        return view('nilai.index', compact('nilais', 'kelasList', 'mapelList'));
    }

    public function create(Request $request): View
    {
        $siswas = Siswa::with('kelas')->orderBy('nama')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList = Guru::orderBy('nama_guru')->get();
        $selectedSiswaId = $request->get('id_siswa');

        return view('nilai.create', compact('siswas', 'mapelList', 'guruList', 'selectedSiswaId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswas,id',
            'id_mapel' => 'required|exists:mapels,id',
            'id_guru' => 'nullable|exists:gurus,id',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string|max:10',
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        // Cek duplikasi nilai per siswa, mapel, semester, tahun ajaran
        $existing = Nilai::where('id_siswa', $validated['id_siswa'])
            ->where('id_mapel', $validated['id_mapel'])
            ->where('semester', $validated['semester'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', 'Nilai mata pelajaran ini untuk semester dan tahun ajaran tersebut sudah pernah diinput.');
        }

        $calc = Nilai::kalkulasiNilai(
            (float) $validated['nilai_tugas'],
            (float) $validated['nilai_uts'],
            (float) $validated['nilai_uas']
        );

        $validated['nilai_akhir'] = $calc['nilai_akhir'];
        $validated['predikat'] = $calc['predikat'];

        $nilai = Nilai::create($validated);
        $nilai->load(['siswa', 'mapel']);
        ActivityLog::record('NILAI_INPUT', "Input nilai {$nilai->mapel?->nama_mapel} untuk siswa {$nilai->siswa?->nama} (Nilai Akhir: {$nilai->nilai_akhir}).");

        return redirect()->route('web.nilai.index')->with('success', 'Nilai akademik siswa berhasil disimpan.');
    }

    public function edit(string|int $id): View
    {
        $nilai = Nilai::with(['siswa', 'mapel', 'guru'])->findOrFail($id);
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList = Guru::orderBy('nama_guru')->get();

        return view('nilai.edit', compact('nilai', 'mapelList', 'guruList'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $nilai = Nilai::findOrFail($id);

        $validated = $request->validate([
            'id_guru' => 'nullable|exists:gurus,id',
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $calc = Nilai::kalkulasiNilai(
            (float) $validated['nilai_tugas'],
            (float) $validated['nilai_uts'],
            (float) $validated['nilai_uas']
        );

        $validated['nilai_akhir'] = $calc['nilai_akhir'];
        $validated['predikat'] = $calc['predikat'];

        $nilai->update($validated);
        ActivityLog::record('NILAI_UPDATE', "Memperbarui nilai #{$id} untuk siswa {$nilai->siswa?->nama}.");

        return redirect()->route('web.nilai.index')->with('success', 'Nilai akademik berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();
        ActivityLog::record('NILAI_DELETE', "Menghapus nilai #{$id}.");

        return redirect()->route('web.nilai.index')->with('success', 'Nilai berhasil dihapus.');
    }

    /**
     * Cetak Lembar Rapor Akademik Siswa Resmi
     */
    public function cetakRapor(string|int $siswaId, Request $request): View
    {
        $siswa = Siswa::with(['kelas', 'presensis'])->findOrFail($siswaId);
        $semester = $request->get('semester', 'Ganjil');
        $tahunAjaran = $request->get('tahun_ajaran', '2025/2026');

        $nilais = Nilai::with(['mapel', 'guru'])
            ->where('id_siswa', $siswa->id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $kehadiran = [
            'hadir' => $siswa->presensis->where('status', 'Hadir')->count(),
            'izin' => $siswa->presensis->where('status', 'Izin')->count(),
            'sakit' => $siswa->presensis->where('status', 'Sakit')->count(),
            'alpa' => $siswa->presensis->where('status', 'Alpa')->count(),
        ];

        ActivityLog::record('RAPOR_PRINT', "Mencetak rapor akademik siswa {$siswa->nama} (Semester {$semester} {$tahunAjaran}).");

        return view('nilai.rapor', compact('siswa', 'nilais', 'semester', 'tahunAjaran', 'kehadiran'));
    }
}
