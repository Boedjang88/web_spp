<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\PembayaranResource;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    /**
     * Get statistics summary for SPP Dashboard
     */
    public function summary(): JsonResponse
    {
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalSpp = Spp::count();
        
        $totalPemasukan = (int) Pembayaran::sum('jumlah_bayar');
        $totalTransaksi = Pembayaran::count();

        $transaksiHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())->count();
        $pemasukanHariIni = (int) Pembayaran::whereDate('tgl_bayar', now()->toDateString())->sum('jumlah_bayar');

        $transaksiBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->count();
            
        $pemasukanBulanIni = (int) Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');

        $transaksiTerbaru = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $data = [
            'statistik' => [
                'total_siswa' => $totalSiswa,
                'total_kelas' => $totalKelas,
                'total_tarif_spp' => $totalSpp,
                'total_transaksi' => $totalTransaksi,
                'total_pemasukan' => $totalPemasukan,
                'formatted_total_pemasukan' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
                'hari_ini' => [
                    'transaksi' => $transaksiHariIni,
                    'pemasukan' => $pemasukanHariIni,
                    'formatted_pemasukan' => 'Rp ' . number_format($pemasukanHariIni, 0, ',', '.'),
                ],
                'bulan_ini' => [
                    'transaksi' => $transaksiBulanIni,
                    'pemasukan' => $pemasukanBulanIni,
                    'formatted_pemasukan' => 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'),
                ],
            ],
            'transaksi_terbaru' => PembayaranResource::collection($transaksiTerbaru),
        ];

        return $this->sendResponse($data, 'Ringkasan data dashboard SPP berhasil diambil.');
    }
}
