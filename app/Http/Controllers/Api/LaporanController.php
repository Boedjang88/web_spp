<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\PembayaranResource;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends BaseApiController
{
    /**
     * Generate structured financial report & analytics summary
     */
    public function rekap(Request $request): JsonResponse
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_bayar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_bayar', '<=', $request->end_date);
        }

        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }

        if ($request->filled('id_petugas')) {
            $query->where('id_petugas', $request->id_petugas);
        }

        $totalPemasukan = (clone $query)->sum('jumlah_bayar');
        $totalTransaksi = (clone $query)->count();
        $pembayarans = $query->orderBy('tgl_bayar', 'asc')->get();

        $data = [
            'ringkasan' => [
                'periode_mulai' => $request->start_date ?? 'Semua',
                'periode_selesai' => $request->end_date ?? 'Semua',
                'total_transaksi' => $totalTransaksi,
                'total_pemasukan' => (int) $totalPemasukan,
                'formatted_total_pemasukan' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
            ],
            'transaksi' => PembayaranResource::collection($pembayarans),
        ];

        return $this->sendResponse($data, 'Rekapitulasi laporan pembayaran SPP berhasil digenerate.');
    }
}
