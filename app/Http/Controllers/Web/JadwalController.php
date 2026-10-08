<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = JadwalPelajaran::with(['kelas', 'mapel', 'guru']);

        // Scope schedule by user role if student or lecturer
        if ($user && $user->isMahasiswa()) {
            $siswa = $user->siswa ?? $user->mahasiswa ?? \App\Models\Siswa::first();
            if ($siswa && $siswa->id_kelas) {
                $query->where('id_kelas', $siswa->id_kelas);
            }
        } elseif ($user && $user->isDosen()) {
            $guru = $user->guru ?? $user->dosen ?? \App\Models\Guru::first();
            if ($guru) {
                $query->where('id_guru', $guru->id);
            }
        }

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('semester')) {
            $sem = $request->semester;
            $query->whereHas('mapel', function ($q) use ($sem) {
                $q->where(function ($sub) use ($sem) {
                    if (strtolower($sem) === 'ganjil') {
                        $sub->whereIn('semester', [1, 3, 5, 7]);
                    } elseif (strtolower($sem) === 'genap') {
                        $sub->whereIn('semester', [2, 4, 6, 8]);
                    } else {
                        $sub->where('semester', $sem)
                            ->orWhere(function ($s2) use ($sem) {
                                $s2->whereNull('semester')->where('semester_rekomendasi', $sem);
                            });
                    }
                });
            });
        }

        $allJadwals = (clone $query)->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->orderBy('jam_mulai')
            ->get();

        $jadwals = (clone $query)->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->orderBy('jam_mulai')
            ->paginate(15)
            ->withQueryString();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalMingguan = [];
        foreach ($days as $day) {
            $jadwalMingguan[$day] = $allJadwals->where('hari', $day)->values();
        }

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $tahunAkademikAktif = \App\Models\TahunAkademik::where('is_active', true)->first() ?? \App\Models\TahunAkademik::latest()->first();

        return view('jadwal.index', compact('jadwals', 'jadwalMingguan', 'kelasList', 'tahunAkademikAktif', 'user'));
    }

    public function create(): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList = Guru::orderBy('nama_guru')->get();

        return view('jadwal.create', compact('kelasList', 'mapelList', 'guruList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'id_mapel' => 'required|exists:mapels,id',
            'id_guru' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'nullable|string|max:30',
        ], [
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_mapel.required' => 'Mata pelajaran wajib dipilih.',
            'id_guru.required' => 'Guru pengampu wajib dipilih.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $jadwal = JadwalPelajaran::create($validated);
        $jadwal->load(['kelas', 'mapel', 'guru']);
        ActivityLog::record('JADWAL_CREATE', "Menambahkan jadwal pelajaran {$jadwal->mapel?->nama_mapel} kelas {$jadwal->kelas?->nama_kelas} ({$jadwal->hari}).");

        return redirect()->route('web.jadwal.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList = Guru::orderBy('nama_guru')->get();

        return view('jadwal.edit', compact('jadwal', 'kelasList', 'mapelList', 'guruList'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'id_mapel' => 'required|exists:mapels,id',
            'id_guru' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'nullable|string|max:30',
        ]);

        $jadwal->update($validated);
        ActivityLog::record('JADWAL_UPDATE', "Memperbarui jadwal pelajaran #{$jadwal->id}.");

        return redirect()->route('web.jadwal.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();
        ActivityLog::record('JADWAL_DELETE', "Menghapus jadwal pelajaran #{$id}.");

        return redirect()->route('web.jadwal.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
