<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Pembayaran\StorePembayaranRequest;
use App\Http\Requests\Api\Pembayaran\UpdatePembayaranRequest;
use App\Http\Resources\Api\PembayaranResource;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembayaranController extends BaseApiController
{
    /**
     * Display a listing of Pembayaran transactions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('id_siswa')) {
            $query->where('id_siswa', $request->id_siswa);
        }

        if ($request->filled('id_petugas')) {
            $query->where('id_petugas', $request->id_petugas);
        }

        if ($request->filled('bulan_dibayar')) {
            $query->where('bulan_dibayar', $request->bulan_dibayar);
        }

        if ($request->filled('tahun_dibayar')) {
            $query->where('tahun_dibayar', $request->tahun_dibayar);
        }

        if ($request->filled('tgl_bayar')) {
            $query->whereDate('tgl_bayar', $request->tgl_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $pembayarans = $query->orderBy('tgl_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->sendResponse(
            PembayaranResource::collection($pembayarans)->response()->getData(true),
            'Daftar transaksi pembayaran SPP berhasil diambil.'
        );
    }

    /**
     * Store a newly created Pembayaran transaction
     */
    public function store(StorePembayaranRequest $request): JsonResponse
    {
        $siswa = Siswa::with('spp')->findOrFail($request->id_siswa);

        // Check if student already paid for this specific month and year
        $existingPayment = Pembayaran::where('id_siswa', $siswa->id)
            ->where('bulan_dibayar', $request->bulan_dibayar)
            ->where('tahun_dibayar', $request->tahun_dibayar)
            ->first();

        if ($existingPayment) {
            return $this->sendError(
                "Siswa {$siswa->nama} sudah melunasi SPP bulan {$request->bulan_dibayar} {$request->tahun_dibayar}.",
                ['bulan_dibayar' => ['Pembayaran untuk periode ini sudah tercatat sebelumnya.']],
                422
            );
        }

        $idSpp = $request->id_spp ?? $siswa->id_spp;
        $spp = Spp::find($idSpp);
        $jumlahBayar = $request->jumlah_bayar ?? ($spp ? $spp->nominal : 0);

        $pembayaran = Pembayaran::create([
            'id_petugas' => auth()->id() ?? 1,
            'id_siswa' => $siswa->id,
            'tgl_bayar' => $request->tgl_bayar ?? now()->toDateString(),
            'bulan_dibayar' => $request->bulan_dibayar,
            'tahun_dibayar' => (string) $request->tahun_dibayar,
            'id_spp' => $idSpp,
            'jumlah_bayar' => $jumlahBayar,
        ]);

        $pembayaran->load(['petugas', 'siswa.kelas', 'spp']);

        return $this->sendResponse(
            new PembayaranResource($pembayaran),
            'Transaksi pembayaran SPP berhasil dicatat.',
            201
        );
    }

    /**
     * Store multiple (batch) Pembayaran transactions
     */
    public function batchStore(Request $request): JsonResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswas,id',
            'bulan_list' => 'required|array|min:1',
            'bulan_list.*' => 'required|string',
            'tahun_dibayar' => 'required|integer|digits:4',
            'tgl_bayar' => 'nullable|date',
        ]);

        $siswa = Siswa::with('spp')->findOrFail($request->id_siswa);
        $idSpp = $siswa->id_spp;
        $spp = Spp::find($idSpp);
        $nominal = $spp ? $spp->nominal : 0;

        $created = [];
        $skipped = [];

        foreach ($request->bulan_list as $bulan) {
            $existing = Pembayaran::where('id_siswa', $siswa->id)
                ->where('bulan_dibayar', $bulan)
                ->where('tahun_dibayar', (string) $request->tahun_dibayar)
                ->first();

            if ($existing) {
                $skipped[] = $bulan;
                continue;
            }

            $p = Pembayaran::create([
                'id_petugas' => auth()->id() ?? 1,
                'id_siswa' => $siswa->id,
                'tgl_bayar' => $request->tgl_bayar ?? now()->toDateString(),
                'bulan_dibayar' => $bulan,
                'tahun_dibayar' => (string) $request->tahun_dibayar,
                'id_spp' => $idSpp,
                'jumlah_bayar' => $nominal,
            ]);
            $p->load(['petugas', 'siswa.kelas', 'spp']);
            $created[] = $p;
        }

        if (empty($created)) {
            return $this->sendError(
                'Semua bulan yang dipilih sudah lunas.',
                ['bulan_list' => ['Semua bulan dalam daftar sudah dibayar sebelumnya.']],
                422
            );
        }

        return $this->sendResponse([
            'total_transaksi' => count($created),
            'total_rupiah' => count($created) * $nominal,
            'bulan_dibayar' => array_map(fn($p) => $p->bulan_dibayar, $created),
            'bulan_dilewati' => $skipped,
            'transaksi' => PembayaranResource::collection(collect($created)),
        ], 'Transaksi pembayaran batch berhasil dicatat.', 201);
    }

    /**
     * Display the specified Pembayaran
     */
    public function show(string|int $id): JsonResponse
    {
        $pembayaran = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])->find($id);

        if (!$pembayaran) {
            return $this->sendError('Data transaksi pembayaran tidak ditemukan.', [], 404);
        }

        return $this->sendResponse(
            new PembayaranResource($pembayaran),
            'Detail transaksi pembayaran berhasil diambil.'
        );
    }

    /**
     * Update the specified Pembayaran
     */
    public function update(UpdatePembayaranRequest $request, string|int $id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return $this->sendError('Data transaksi pembayaran tidak ditemukan.', [], 404);
        }

        $pembayaran->update($request->validated());
        $pembayaran->load(['petugas', 'siswa.kelas', 'spp']);

        return $this->sendResponse(
            new PembayaranResource($pembayaran),
            'Data transaksi pembayaran berhasil diperbarui.'
        );
    }

    /**
     * Remove the specified Pembayaran
     */
    public function destroy(string|int $id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return $this->sendError('Data transaksi pembayaran tidak ditemukan.', [], 404);
        }

        $pembayaran->delete();

        return $this->sendResponse(null, 'Data transaksi pembayaran berhasil dibatalkan/dihapus.');
    }

    /**
     * Get digital receipt (Kwitansi) data for the specified payment
     */
    public function kwitansi(string|int $id): JsonResponse
    {
        $pembayaran = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])->find($id);

        if (!$pembayaran) {
            return $this->sendError('Data transaksi pembayaran tidak ditemukan.', [], 404);
        }

        $receiptData = [
            'nomor_kwitansi' => 'KWT-' . str_pad((string) $pembayaran->id, 6, '0', STR_PAD_LEFT),
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i:s'),
            'tanggal_bayar' => $pembayaran->tgl_bayar,
            'siswa' => [
                'nisn' => $pembayaran->siswa?->nisn,
                'nis' => $pembayaran->siswa?->nis,
                'nama' => $pembayaran->siswa?->nama,
                'kelas' => $pembayaran->siswa?->kelas?->nama_kelas,
            ],
            'rincian' => [
                'periode_pembayaran' => "SPP Bulan {$pembayaran->bulan_dibayar} {$pembayaran->tahun_dibayar}",
                'tahun_spp' => $pembayaran->spp?->tahun,
                'jumlah_bayar' => (int) $pembayaran->jumlah_bayar,
                'terbilang' => $this->terbilang($pembayaran->jumlah_bayar) . ' rupiah',
                'formatted_jumlah_bayar' => 'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.'),
            ],
            'petugas' => [
                'id' => $pembayaran->petugas?->id,
                'nama' => $pembayaran->petugas?->name,
            ],
            'status' => 'LUNAS',
        ];

        return $this->sendResponse($receiptData, 'Data kwitansi pembayaran berhasil diambil.');
    }

    /**
     * Helper to convert number to Indonesian words
     */
    private function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang((int) ($angka / 10)) . ' puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang((int) ($angka / 100)) . ' ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang((int) ($angka / 1000)) . ' ribu' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang((int) ($angka / 1000000)) . ' juta' . $this->terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }
}
