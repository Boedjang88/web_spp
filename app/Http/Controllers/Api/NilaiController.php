<?php

namespace App\Http\Controllers\Api;

use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NilaiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Nilai::with(['siswa.kelas', 'mapel', 'guru']);

        if ($request->filled('id_siswa')) {
            $query->where('id_siswa', $request->id_siswa);
        }

        if ($request->filled('id_mapel')) {
            $query->where('id_mapel', $request->id_mapel);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        $nilais = $query->orderBy('id', 'desc')->paginate((int) $request->get('per_page', 20));

        return $this->sendResponse($nilais, 'Daftar nilai akademik siswa berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
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

        $existing = Nilai::where('id_siswa', $validated['id_siswa'])
            ->where('id_mapel', $validated['id_mapel'])
            ->where('semester', $validated['semester'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->first();

        if ($existing) {
            return $this->sendError('Nilai mata pelajaran ini untuk semester dan tahun ajaran tersebut sudah pernah diinput.', [], 422);
        }

        $calc = Nilai::kalkulasiNilai(
            (float) $validated['nilai_tugas'],
            (float) $validated['nilai_uts'],
            (float) $validated['nilai_uas']
        );

        $validated['nilai_akhir'] = $calc['nilai_akhir'];
        $validated['predikat'] = $calc['predikat'];

        $nilai = Nilai::create($validated);
        $nilai->load(['siswa.kelas', 'mapel', 'guru']);

        return $this->sendResponse($nilai, 'Nilai akademik berhasil ditambahkan.', 201);
    }

    public function show(string|int $id): JsonResponse
    {
        $nilai = Nilai::with(['siswa.kelas', 'mapel', 'guru'])->find($id);

        if (!$nilai) {
            return $this->sendError('Data nilai tidak ditemukan.', [], 404);
        }

        return $this->sendResponse($nilai, 'Detail nilai berhasil diambil.');
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return $this->sendError('Data nilai tidak ditemukan.', [], 404);
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
        $nilai->load(['siswa.kelas', 'mapel', 'guru']);

        return $this->sendResponse($nilai, 'Data nilai berhasil diperbarui.');
    }

    public function destroy(string|int $id): JsonResponse
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return $this->sendError('Data nilai tidak ditemukan.', [], 404);
        }

        $nilai->delete();

        return $this->sendResponse(null, 'Data nilai berhasil dihapus.');
    }

    /**
     * Endpoint API E-Rapor Siswa
     */
    public function rapor(string|int $siswaId, Request $request): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'presensis'])->find($siswaId);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

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

        $rataRata = $nilais->count() > 0 ? round($nilais->avg('nilai_akhir'), 2) : 0;

        $raporData = [
            'identitas_universitas' => [
                'nama' => 'Universitas SIAKAD Enterprise',
                'alamat' => 'Jl. Kampus Utama No. 123, Indonesia',
                'akreditasi' => 'Unggul (A)',
            ],
            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas?->nama_kelas,
                'kompetensi_keahlian' => $siswa->kelas?->kompetensi_keahlian,
                'semester' => $semester,
                'tahun_ajaran' => $tahunAjaran,
            ],
            'ringkasan_akademik' => [
                'total_mapel' => $nilais->count(),
                'rata_rata_nilai' => $rataRata,
                'predikat_umum' => $rataRata >= 85 ? 'Sangat Baik' : ($rataRata >= 75 ? 'Baik' : 'Cukup'),
            ],
            'nilai_mata_pelajaran' => $nilais->map(function ($n) {
                return [
                    'id' => $n->id,
                    'kode_mapel' => $n->mapel?->kode_mapel,
                    'nama_mapel' => $n->mapel?->nama_mapel,
                    'kelompok' => $n->mapel?->kelompok,
                    'kkm' => $n->mapel?->kkm,
                    'guru_pengampu' => $n->guru?->nama_guru ?? 'Guru Mapel',
                    'nilai_tugas' => (float) $n->nilai_tugas,
                    'nilai_uts' => (float) $n->nilai_uts,
                    'nilai_uas' => (float) $n->nilai_uas,
                    'nilai_akhir' => (float) $n->nilai_akhir,
                    'predikat' => $n->predikat,
                    'tuntas' => $n->nilai_akhir >= ($n->mapel?->kkm ?? 75),
                    'catatan' => $n->catatan,
                ];
            }),
            'rekap_kehadiran' => $kehadiran,
        ];

        return $this->sendResponse($raporData, 'Data E-Rapor siswa berhasil digenerate.');
    }
}
