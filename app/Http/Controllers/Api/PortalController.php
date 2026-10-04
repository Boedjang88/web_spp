<?php

namespace App\Http\Controllers\Api;

use App\Models\Siswa;
use Illuminate\Http\JsonResponse;

class PortalController extends BaseApiController
{
    /**
     * Public endpoint to check student SPP status & invoice by NISN
     */
    public function cekSiswa(string $nisn): JsonResponse
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans.petugas', 'nilais.mapel', 'nilais.guru', 'presensis'])
            ->where('nisn', $nisn)
            ->first();

        if (!$siswa) {
            return $this->sendError(
                'Data mahasiswa dengan NIM / NISN tersebut tidak ditemukan.',
                ['nisn' => ['NIM / NISN tidak terdaftar dalam basis data universitas.']],
                404
            );
        }

        $infoTunggakan = $siswa->info_tunggakan;

        $response = [
            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'alamat' => $siswa->alamat,
                'no_telp' => $siswa->no_telp,
                'kelas' => [
                    'id' => $siswa->kelas?->id,
                    'nama_kelas' => $siswa->kelas?->nama_kelas,
                    'kompetensi_keahlian' => $siswa->kelas?->kompetensi_keahlian,
                ],
                'spp' => [
                    'id' => $siswa->spp?->id,
                    'tahun' => $siswa->spp?->tahun,
                    'nominal' => $siswa->spp?->nominal,
                    'formatted_nominal' => 'Rp ' . number_format($siswa->spp?->nominal ?? 0, 0, ',', '.'),
                ],
            ],
            'akademik' => [
                'total_mapel_dinilai' => $siswa->nilais->count(),
                'rata_rata_nilai' => $siswa->nilais->count() > 0 ? round($siswa->nilais->avg('nilai_akhir'), 2) : 0,
                'daftar_nilai' => $siswa->nilais->map(fn($n) => [
                    'mapel' => $n->mapel?->nama_mapel,
                    'kkm' => $n->mapel?->kkm,
                    'nilai_akhir' => (float) $n->nilai_akhir,
                    'predikat' => $n->predikat,
                    'tuntas' => $n->nilai_akhir >= ($n->mapel?->kkm ?? 75),
                    'semester' => $n->semester,
                    'tahun_ajaran' => $n->tahun_ajaran,
                ]),
                'rekap_kehadiran' => [
                    'hadir' => $siswa->presensis->where('status', 'Hadir')->count(),
                    'izin' => $siswa->presensis->where('status', 'Izin')->count(),
                    'sakit' => $siswa->presensis->where('status', 'Sakit')->count(),
                    'alpa' => $siswa->presensis->where('status', 'Alpa')->count(),
                ],
            ],
            'ringkasan_keuangan' => [
                'status_lunas' => $infoTunggakan['total_bulan'] === 0,
                'total_bulan_tunggakan' => $infoTunggakan['total_bulan'],
                'total_rupiah_tunggakan' => $infoTunggakan['total_rupiah'],
                'formatted_tunggakan' => 'Rp ' . number_format($infoTunggakan['total_rupiah'], 0, ',', '.'),
                'bulan_nunggak' => $infoTunggakan['list_bulan'],
                'list_bulan' => $infoTunggakan['list_bulan'],
                'total_terbayar_rupiah' => (int) $siswa->pembayarans->sum('jumlah_bayar'),
                'formatted_total_terbayar' => 'Rp ' . number_format($siswa->pembayarans->sum('jumlah_bayar'), 0, ',', '.'),
                'total_transaksi' => $siswa->pembayarans->count(),
            ],
            'riwayat_pembayaran' => $siswa->pembayarans->sortByDesc('tgl_bayar')->values()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nomor_kwitansi' => 'KWT-' . str_pad((string) $p->id, 6, '0', STR_PAD_LEFT),
                    'tgl_bayar' => $p->tgl_bayar,
                    'bulan_dibayar' => $p->bulan_dibayar,
                    'tahun_dibayar' => $p->tahun_dibayar,
                    'jumlah_bayar' => (int) $p->jumlah_bayar,
                    'formatted_jumlah_bayar' => 'Rp ' . number_format($p->jumlah_bayar, 0, ',', '.'),
                    'petugas' => $p->petugas?->name ?? 'System',
                ];
            }),
        ];

        return $this->sendResponse($response, 'Data akademik, tagihan, dan riwayat pembayaran siswa berhasil ditemukan.');
    }
}
