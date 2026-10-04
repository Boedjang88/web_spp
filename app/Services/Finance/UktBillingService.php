<?php

namespace App\Services\Finance;

use App\Models\FinancialClearance;
use App\Models\Mahasiswa;
use App\Models\PembayaranUkt;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\Ukt;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UktBillingService
{
    /**
     * Generate or fetch semester UKT bill with complete itemized breakdown.
     */
    public function generateSemesterBill(Mahasiswa $mahasiswa, TahunAkademik $tahunAkademik): TagihanUkt
    {
        $existing = TagihanUkt::where('id_mahasiswa', $mahasiswa->id)
            ->where('id_tahun_akademik', $tahunAkademik->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $ukt = $mahasiswa->ukt ?? Ukt::where('id_prodi', $mahasiswa->id_prodi)->first();

        $biayaUkt = (float) ($ukt?->nominal ?? 4500000.00);
        $biayaPraktikum = (float) ($ukt?->biaya_praktikum ?? 500000.00);
        $biayaKemahasiswaan = (float) ($ukt?->biaya_kemahasiswaan ?? 50000.00);
        $totalTagihan = $biayaUkt + $biayaPraktikum + $biayaKemahasiswaan;

        $prodiKode = $mahasiswa->prodi?->kode_prodi ?? '01';
        $nomorVa = '988' . str_pad($prodiKode, 3, '0', STR_PAD_LEFT) . preg_replace('/[^0-9]/', '', $mahasiswa->nim);
        $nomorInvoice = 'INV-UKT-' . $tahunAkademik->kode_tahun . '-' . strtoupper(Str::random(6));

        return TagihanUkt::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_tahun_akademik' => $tahunAkademik->id,
            'nomor_va' => $nomorVa,
            'nomor_invoice' => $nomorInvoice,
            'biaya_ukt' => $biayaUkt,
            'biaya_praktikum' => $biayaPraktikum,
            'biaya_kemahasiswaan' => $biayaKemahasiswaan,
            'total_tagihan' => $totalTagihan,
            'total_potongan_beasiswa' => 0.00,
            'total_harus_bayar' => $totalTagihan,
            'total_sudah_bayar' => 0.00,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);
    }

    /**
     * Process incoming H2H bank settlement callback for UKT payment.
     */
    public function processUktCallback(
        string $nomorVa,
        string $nomorTransaksiBank,
        float $jumlahBayar,
        string $kodeBank = 'BNI',
        string $channelBayar = 'H2H_ATM'
    ): PembayaranUkt {
        return DB::transaction(function () use ($nomorVa, $nomorTransaksiBank, $jumlahBayar, $kodeBank, $channelBayar) {
            $tagihan = TagihanUkt::with('mahasiswa')->where('nomor_va', $nomorVa)->lockForUpdate()->first();

            if (!$tagihan) {
                throw new DomainException("Tagihan UKT dengan nomor Virtual Account {$nomorVa} tidak ditemukan.");
            }

            // Check duplicate payment transaction
            $existingPayment = PembayaranUkt::where('nomor_transaksi_bank', $nomorTransaksiBank)->first();
            if ($existingPayment) {
                return $existingPayment;
            }

            $tagihan->total_sudah_bayar += $jumlahBayar;
            if ($tagihan->total_sudah_bayar >= $tagihan->total_harus_bayar) {
                $tagihan->status_pembayaran = 'Lunas';
                $tagihan->tgl_lunas = now();
            } else {
                $tagihan->status_pembayaran = 'Sebagian';
            }
            $tagihan->save();

            // Find or fallback to admin user
            $userId = User::where('id_mahasiswa', $tagihan->id_mahasiswa)->value('id')
                ?? User::where('id_siswa', $tagihan->id_mahasiswa)->value('id')
                ?? 1;

            $nomorKuitansi = 'KWT-UKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $pembayaran = PembayaranUkt::create([
                'id_user' => $userId,
                'id_mahasiswa' => $tagihan->id_mahasiswa,
                'id_tagihan_ukt' => $tagihan->id,
                'id_ukt' => $tagihan->mahasiswa?->id_ukt,
                'tgl_bayar' => now(),
                'semester_dibayar' => $tagihan->tahunAkademik?->semester ?? 'Genap',
                'tahun_dibayar' => (string) date('Y'),
                'jumlah_bayar' => $jumlahBayar,
                'channel_bayar' => $channelBayar,
                'nomor_transaksi_bank' => $nomorTransaksiBank,
                'kode_bank' => $kodeBank,
                'nomor_kuitansi' => $nomorKuitansi,
                'status_transaksi' => 'SUCCESS',
            ]);

            // Release Financial Clearance for Smart KRS
            if ($tagihan->status_pembayaran === 'Lunas') {
                FinancialClearance::updateOrCreate(
                    [
                        'id_siswa' => $tagihan->id_mahasiswa,
                        'id_tahun_akademik' => $tagihan->id_tahun_akademik,
                    ],
                    [
                        'is_cleared' => true,
                        'status' => 'CLEARED',
                        'catatan' => 'Lunas via H2H UKT Settlement.',
                        'cleared_at' => now(),
                    ]
                );
            }

            return $pembayaran;
        });
    }
}
