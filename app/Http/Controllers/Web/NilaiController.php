<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Nilai::with(['siswa.kelas', 'mapel', 'guru']);

        if ($user && $user->isMahasiswa()) {
            $siswa = $user->siswa ?? $user->mahasiswa;
            abort_if(!$siswa, 403, 'Profil mahasiswa belum terhubung dengan akun Anda.');
            $query->where('id_siswa', $siswa->id);
        }

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
        $selectedSiswaId = $request->get('id_siswa');
        $idKelas = $request->get('id_kelas');
        $search = $request->get('search');

        $siswasQuery = Siswa::with('kelas')->orderBy('nama');
        if ($idKelas) {
            $siswasQuery->where('id_kelas', $idKelas);
        }
        if ($search) {
            $siswasQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        $siswas = $siswasQuery->limit(100)->get();

        if ($selectedSiswaId && !$siswas->contains('id', $selectedSiswaId)) {
            $selectedSiswa = Siswa::with('kelas')->find($selectedSiswaId);
            if ($selectedSiswa) {
                $siswas->prepend($selectedSiswa);
            }
        }

        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList = Guru::orderBy('nama_guru')->get();

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

        // Enforce grade entry deadline lock
        $activeTa = TahunAkademik::where('semester', $validated['semester'])
            ->where(function ($q) use ($validated) {
                $q->where('nama_tahun', $validated['tahun_ajaran'])
                  ->orWhere('kode_tahun', 'like', '%' . substr($validated['tahun_ajaran'], 0, 4) . '%');
            })
            ->first() ?? TahunAkademik::where('is_active', true)->first();

        $user = auth()->user();
        if ($activeTa && !$activeTa->isGradingOpen() && $user && !$user->isAdmin() && !$user->isSuperAdmin()) {
            return back()->withInput()->with('error', 'Batas waktu penginputan nilai untuk periode akademik ini telah berakhir atau belum dibuka. Hubungi BAAK untuk dispensasi.');
        }

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
        $this->syncToKrsDetail($nilai);
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

        $activeTa = TahunAkademik::where('semester', $nilai->semester)
            ->where(function ($q) use ($nilai) {
                $q->where('nama_tahun', $nilai->tahun_ajaran)
                  ->orWhere('kode_tahun', 'like', '%' . substr($nilai->tahun_ajaran, 0, 4) . '%');
            })
            ->first() ?? TahunAkademik::where('is_active', true)->first();

        $user = auth()->user();
        if ($activeTa && !$activeTa->isGradingOpen() && $user && !$user->isAdmin() && !$user->isSuperAdmin()) {
            return back()->withInput()->with('error', 'Periode pengubahan nilai untuk semester ini telah terkunci. Perubahan nilai harus melalui persetujuan resmi BAAK.');
        }

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
        $this->syncToKrsDetail($nilai);
        ActivityLog::record('NILAI_UPDATE', "Memperbarui nilai #{$id} untuk siswa {$nilai->siswa?->nama}.");

        return redirect()->route('web.nilai.index')->with('success', 'Nilai akademik berhasil diperbarui.');
    }

    protected function syncToKrsDetail(Nilai $nilai): void
    {
        try {
            $krs = \App\Models\Krs::where('id_siswa', $nilai->id_siswa)->latest()->first();
            if ($krs) {
                $kelasKuliah = \App\Models\KelasKuliah::where('id_mk', $nilai->id_mapel)->first();
                if ($kelasKuliah) {
                    \App\Models\KrsDetail::updateOrCreate(
                        [
                            'id_krs' => $krs->id,
                            'id_kelas_kuliah' => $kelasKuliah->id,
                        ],
                        [
                            'nilai_tugas' => $nilai->nilai_tugas,
                            'nilai_uts' => $nilai->nilai_uts,
                            'nilai_uas' => $nilai->nilai_uas,
                            'nilai_akhir_angka' => $nilai->nilai_akhir,
                            'nilai_akhir_huruf' => $nilai->predikat,
                            'bobot_mutu' => match($nilai->predikat) {
                                'A' => 4.00,
                                'B' => 3.00,
                                'C' => 2.00,
                                'D' => 1.00,
                                default => 0.00,
                            },
                            'is_lulus' => $nilai->nilai_akhir >= 75,
                            'is_published' => true,
                        ]
                    );

                    if (class_exists(\App\Services\Academic\GradingService::class)) {
                        app(\App\Services\Academic\GradingService::class)->calculateIps($krs);
                    }
                }
            }
        } catch (\Throwable $e) {}
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();
        ActivityLog::record('NILAI_DELETE', "Menghapus nilai #{$id}.");

        return redirect()->route('web.nilai.index')->with('success', 'Nilai berhasil dihapus.');
    }

    /**
     * Cetak Kartu Hasil Studi (E-KHS) Mahasiswa Resmi
     */
    public function cetakKhs(string|int $siswaId, Request $request): View
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['siswa', 'mahasiswa'])) {
            $studentId = $user->id_siswa ?? $user->id_mahasiswa;
            abort_if(!$studentId || (int)$studentId !== (int)$siswaId, 403, 'Akses ditolak: Anda hanya dapat mencetak KHS milik sendiri.');
        }

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

        ActivityLog::record('KHS_PRINT', "Mencetak kartu hasil studi (E-KHS) mahasiswa {$siswa->nama} (Semester {$semester} {$tahunAjaran}).");

        $viewName = view()->exists('nilai.khs') ? 'nilai.khs' : 'nilai.rapor';
        return view($viewName, compact('siswa', 'nilais', 'semester', 'tahunAjaran', 'kehadiran'));
    }

    /**
     * Cetak Lembar Rapor / KHS Mahasiswa Resmi (Backward-compatible alias)
     */
    public function cetakRapor(string|int $siswaId, Request $request): View
    {
        return $this->cetakKhs($siswaId, $request);
    }
}
