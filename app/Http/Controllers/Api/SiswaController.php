<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Siswa\StoreSiswaRequest;
use App\Http\Requests\Api\Siswa\UpdateSiswaRequest;
use App\Http\Resources\Api\SiswaResource;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends BaseApiController
{
    /**
     * Display a listing of Siswa
     */
    public function index(Request $request): JsonResponse
    {
        $query = Siswa::with(['kelas', 'spp']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        if ($request->filled('id_spp')) {
            $query->where('id_spp', $request->id_spp);
        }

        $perPage = (int) $request->get('per_page', 15);
        $siswas = $query->orderBy('nama')->paginate($perPage);

        return $this->sendResponse(
            SiswaResource::collection($siswas)->response()->getData(true),
            'Daftar siswa berhasil diambil.'
        );
    }

    /**
     * Store a newly created Siswa
     */
    public function store(StoreSiswaRequest $request): JsonResponse
    {
        $siswa = Siswa::create($request->validated());
        $siswa->load(['kelas', 'spp']);

        \App\Models\ActivityLog::record('SISWA_CREATE_API', "Menambahkan data siswa {$siswa->nama} via REST API.");

        return $this->sendResponse(
            new SiswaResource($siswa),
            'Data siswa berhasil ditambahkan.',
            201
        );
    }

    /**
     * Display the specified Siswa with full relations and tunggakan info
     */
    public function show(string|int $id): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans.petugas'])->find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        return $this->sendResponse(
            new SiswaResource($siswa),
            'Detail siswa berhasil diambil.'
        );
    }

    /**
     * Update the specified Siswa
     */
    public function update(UpdateSiswaRequest $request, string|int $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        $siswa->update($request->validated());
        $siswa->load(['kelas', 'spp']);

        \App\Models\ActivityLog::record('SISWA_UPDATE_API', "Memperbarui data siswa {$siswa->nama} via REST API.");

        return $this->sendResponse(
            new SiswaResource($siswa),
            'Data siswa berhasil diperbarui.'
        );
    }

    /**
     * Remove the specified Siswa
     */
    public function destroy(string|int $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        if ($siswa->pembayarans()->count() > 0) {
            return $this->sendError("Siswa {$siswa->nama} tidak dapat dihapus karena memiliki riwayat pembayaran SPP.", [], 400);
        }

        $nama = $siswa->nama;
        $siswa->delete();

        \App\Models\ActivityLog::record('SISWA_DELETE_API', "Menghapus data siswa {$nama} via REST API.");

        return $this->sendResponse(null, 'Data siswa berhasil dihapus.');
    }

    /**
     * Get official Exam Pass (Kartu Peserta Ujian) data with SPP clearance check
     */
    public function kartuUjian(string|int $id): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'spp'])->find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        // Security check for Siswa role: cannot view other students' exam pass
        if (auth()->check() && auth()->user()->role === 'siswa') {
            if (auth()->user()->id_siswa && auth()->user()->id_siswa != $siswa->id) {
                return $this->sendError('Akses ditolak. Anda hanya dapat melihat kartu ujian milik Anda sendiri.', [], 403);
            }
        }

        $tunggakan = $siswa->info_tunggakan;
        $isLunas = $tunggakan['total_bulan'] === 0;

        $kartuData = [
            'is_eligible' => $isLunas,
            'status_administrasi' => $isLunas ? 'LUNAS (Bebas Tanggungan)' : 'MENUNGGAK',
            'nomor_ujian' => 'UAS-' . $siswa->nisn . '-' . date('Y'),
            'tahun_ajaran' => '2025/2026',
            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas?->nama_kelas,
                'kompetensi_keahlian' => $siswa->kelas?->kompetensi_keahlian,
            ],
            'tunggakan' => $tunggakan,
            'qr_verification_code' => 'VERIFIED-LUNAS-' . substr(md5((string) $siswa->nisn), 0, 10),
            'pesan' => $isLunas
                ? 'Siswa berhak mengikuti Ujian Akhir Semester.'
                : 'Kartu Ujian terkunci. Harap menyelesaikan pembayaran SPP tertunggak sebesar Rp ' . number_format($tunggakan['total_rupiah'], 0, ',', '.') . ' (' . $tunggakan['total_bulan'] . ' bulan).',
        ];

        return $this->sendResponse($kartuData, 'Data kartu peserta ujian siswa berhasil diproses.');
    }

    /**
     * Check billing and arrears status for specific student
     */
    public function tunggakan(string|int $id): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'spp'])->find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        $infoTunggakan = $siswa->info_tunggakan;

        return $this->sendResponse([
            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas?->nama_kelas,
                'nominal_spp' => $siswa->spp?->nominal,
                'formatted_nominal_spp' => 'Rp ' . number_format($siswa->spp?->nominal ?? 0, 0, ',', '.'),
            ],
            'tunggakan' => $infoTunggakan,
        ], 'Informasi tagihan & tunggakan SPP siswa berhasil dihitung.');
    }

    /**
     * Get official Surat Tagihan (statement of account) document data
     */
    public function suratTagihan(string|int $id): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans'])->find($id);

        if (!$siswa) {
            return $this->sendError('Data siswa tidak ditemukan.', [], 404);
        }

        $tunggakan = $siswa->info_tunggakan;

        $suratData = [
            'nomor_surat' => '421.5/SPP-' . str_pad((string) $siswa->id, 4, '0', STR_PAD_LEFT) . '/' . date('Y'),
            'tanggal_surat' => now()->translatedFormat('d F Y'),
            'institusi' => [
                'nama' => 'SMK Merdeka Belajar',
                'alamat' => 'Jl. Pendidikan No. 45, Kompleks Akademika',
                'kontak' => 'Telp. (021) 789-0123 | Email: tu@smkmerdeka.sch.id',
            ],
            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas?->nama_kelas,
                'kompetensi_keahlian' => $siswa->kelas?->kompetensi_keahlian,
                'alamat' => $siswa->alamat,
                'no_telp' => $siswa->no_telp,
            ],
            'tarif_spp_bulanan' => (int) ($siswa->spp?->nominal ?? 0),
            'tahun_ajaran' => $siswa->spp?->tahun,
            'total_tunggakan_rupiah' => $tunggakan['total_rupiah'],
            'total_bulan_tunggakan' => $tunggakan['total_bulan'],
            'bulan_nunggak' => $tunggakan['list_bulan'],
            'status' => $tunggakan['total_bulan'] === 0 ? 'LUNAS' : 'MENUNGGAK',
            'metode_pembayaran' => [
                'loket_sekolah' => 'Loket Kasir Tata Usaha Sekolah (Senin-Jumat 07.30-15.00 WIB)',
                'virtual_account_bni' => '988-1234-' . $siswa->nisn,
            ],
        ];

        return $this->sendResponse($suratData, 'Data surat tagihan resmi siswa berhasil digenerate.');
    }
}
