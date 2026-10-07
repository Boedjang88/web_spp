<?php

namespace App\Services\Finance;

use App\Models\BankMitra;
use App\Models\FinancialClearance;
use App\Models\Siswa;
use App\Models\TagihanVa;
use App\Models\TagihanVaItem;
use App\Models\TahunAkademik;
use App\Models\TransaksiH2h;
use App\Services\Audit\AuditTrailService;
use Exception;
use Illuminate\Support\Facades\DB;

class H2hBillingService
{
    public function __construct(
        protected AuditTrailService $auditService
    ) {}

    /**
     * Generate dynamic Virtual Account and Invoices for a student cohort
     */
    public function generateSemesterInvoice(
        int $idSiswa,
        int $idTahunAkademik,
        int $idBankMitra,
        array $items,
        float $beasiswaDiskon = 0.00
    ): TagihanVa {
        return DB::transaction(function () use ($idSiswa, $idTahunAkademik, $idBankMitra, $items, $beasiswaDiskon) {
            $siswa = Siswa::findOrFail($idSiswa);
            $tahun = TahunAkademik::findOrFail($idTahunAkademik);
            $bank = BankMitra::findOrFail($idBankMitra);

            // Generate structured VA Number (Prefix + NIM/NISN)
            $cleanNim = preg_replace('/[^0-9]/', '', (string) ($siswa->nisn ?: $siswa->nis ?: $siswa->id));
            $nomorVa = $bank->prefix_va . str_pad($cleanNim, 11, '0', STR_PAD_LEFT);
            $nomorInvoice = 'INV/' . $tahun->kode_tahun . '/' . date('Ymd') . '/' . $siswa->id;

            $totalTagihan = 0.00;
            foreach ($items as $item) {
                $totalTagihan += (float) ($item['nominal'] ?? 0);
            }

            $totalHarusBayar = max(0.00, $totalTagihan - $beasiswaDiskon);

            $tagihan = TagihanVa::updateOrCreate(
                [
                    'id_siswa' => $idSiswa,
                    'id_tahun_akademik' => $idTahunAkademik,
                ],
                [
                    'id_bank_mitra' => $idBankMitra,
                    'nomor_va' => $nomorVa,
                    'nomor_invoice' => $nomorInvoice,
                    'total_tagihan' => $totalTagihan,
                    'total_potongan_beasiswa' => $beasiswaDiskon,
                    'total_harus_bayar' => $totalHarusBayar,
                    'total_sudah_bayar' => 0.00,
                    'status_pembayaran' => $totalHarusBayar == 0 ? 'Lunas' : 'Belum Bayar',
                    'tgl_jatuh_tempo' => now()->addDays(30),
                    'tgl_lunas' => $totalHarusBayar == 0 ? now() : null,
                ]
            );

            // Save line items
            $tagihan->items()->delete();
            foreach ($items as $item) {
                TagihanVaItem::create([
                    'id_tagihan_va' => $tagihan->id,
                    'nama_item' => $item['nama_item'],
                    'nominal' => $item['nominal'],
                ]);
            }

            // If zero bill (full scholarship), immediately unlock KRS
            if ($totalHarusBayar == 0) {
                FinancialClearance::updateOrCreate(
                    [
                        'id_siswa' => $idSiswa,
                        'id_tahun_akademik' => $idTahunAkademik,
                    ],
                    [
                        'is_krs_unlocked' => true,
                        'is_uts_unlocked' => true,
                        'is_uas_unlocked' => true,
                        'unlocked_at' => now(),
                        'unlocked_by_channel' => 'SCHOLARSHIP_AUTO',
                    ]
                );
            }

            return $tagihan;
        });
    }

    /**
     * Process Bank Callback / Webhook Payload in Real-Time (< 1 second)
     *
     * @throws Exception
     */
    public function processH2hPaymentCallback(
        string $nomorVa,
        string $nomorTransaksiBank,
        float $jumlahBayar,
        string $kodeBank,
        string $channelBayar,
        array $rawPayload,
        ?string $providedSignature = null
    ): TransaksiH2h {
        return DB::transaction(function () use (
            $nomorVa,
            $nomorTransaksiBank,
            $jumlahBayar,
            $kodeBank,
            $channelBayar,
            $rawPayload,
            $providedSignature
        ) {
            // 1. Idempotency Check: Prevent duplicate payment recording
            $existingTx = TransaksiH2h::where('nomor_transaksi_bank', $nomorTransaksiBank)->first();
            if ($existingTx) {
                return $existingTx;
            }

            // 2. Locate Tagihan VA or Tagihan UKT
            $tagihan = TagihanVa::where('nomor_va', $nomorVa)->lockForUpdate()->first();
            
            if (!$tagihan) {
                $tagihanUkt = \App\Models\TagihanUkt::where('nomor_va', $nomorVa)->lockForUpdate()->first();
                if ($tagihanUkt) {
                    $tagihan = TagihanVa::firstOrCreate(
                        [
                            'id_siswa' => $tagihanUkt->id_mahasiswa,
                            'id_tahun_akademik' => $tagihanUkt->id_tahun_akademik,
                        ],
                        [
                            'nomor_va' => $tagihanUkt->nomor_va,
                            'nomor_invoice' => $tagihanUkt->nomor_invoice,
                            'total_tagihan' => $tagihanUkt->total_tagihan,
                            'total_harus_bayar' => $tagihanUkt->total_harus_bayar,
                            'total_sudah_bayar' => $tagihanUkt->total_sudah_bayar,
                            'status_pembayaran' => $tagihanUkt->status_pembayaran,
                            'tgl_jatuh_tempo' => $tagihanUkt->tgl_jatuh_tempo,
                        ]
                    );

                    // Sync TagihanUkt
                    $tagihanUkt->total_sudah_bayar += $jumlahBayar;
                    if ($tagihanUkt->total_sudah_bayar >= $tagihanUkt->total_harus_bayar) {
                        $tagihanUkt->status_pembayaran = 'Lunas';
                        $tagihanUkt->tgl_lunas = now();
                    } else {
                        $tagihanUkt->status_pembayaran = 'Sebagian';
                    }
                    $tagihanUkt->save();
                }
            }

            if (!$tagihan) {
                throw new Exception("Virtual Account {$nomorVa} tidak terdaftar di sistem.");
            }

            // 3. Verify HMAC Signature if Bank provides secret key
            if ($tagihan->bankMitra && $providedSignature) {
                $expectedSignature = hash_hmac('sha256', $nomorVa . '|' . $nomorTransaksiBank . '|' . $jumlahBayar, $tagihan->bankMitra->secret_key);
                if (!hash_equals($expectedSignature, $providedSignature)) {
                    throw new Exception('Invalid HMAC Security Signature from Bank Gateway.');
                }
            }

            // 4. Record H2H Transaction Ledger
            $transaksi = TransaksiH2h::create([
                'id_tagihan_va' => $tagihan->id,
                'nomor_transaksi_bank' => $nomorTransaksiBank,
                'kode_bank' => $kodeBank,
                'nomor_va' => $nomorVa,
                'jumlah_dibayar' => $jumlahBayar,
                'waktu_transaksi_bank' => now(),
                'channel_bayar' => $channelBayar,
                'signature_hash' => $providedSignature,
                'raw_callback_payload' => $rawPayload,
                'status_callback' => 'SUCCESS',
            ]);

            // 5. Update Invoice State
            $tagihan->total_sudah_bayar += $jumlahBayar;
            if ($tagihan->total_sudah_bayar >= $tagihan->total_harus_bayar) {
                $tagihan->status_pembayaran = 'Lunas';
                $tagihan->tgl_lunas = now();
            } else {
                $tagihan->status_pembayaran = 'Sebagian';
            }
            $tagihan->save();

            // 6. INSTANTLY RELEASE KRS REGISTRATION LOCK (< 1s execution)
            FinancialClearance::updateOrCreate(
                [
                    'id_siswa' => $tagihan->id_siswa,
                    'id_tahun_akademik' => $tagihan->id_tahun_akademik,
                ],
                [
                    'is_krs_unlocked' => true,
                    'is_uts_unlocked' => true,
                    'is_uas_unlocked' => true,
                    'unlocked_at' => now(),
                    'unlocked_by_channel' => 'H2H_WEBHOOK_' . $kodeBank,
                ]
            );

            // 7. Audit Trail
            $this->auditService->record(
                null,
                'H2H_PAYMENT_SUCCESS',
                [
                    'nomor_va' => $nomorVa,
                    'nomor_transaksi_bank' => $nomorTransaksiBank,
                    'jumlah_bayar' => $jumlahBayar,
                    'bank' => $kodeBank,
                    'id_siswa' => $tagihan->id_siswa,
                ]
            );

            return $transaksi;
        });
    }

    /**
     * Generate Dynamic QRIS Code for Instant Payment
     */
    public function generateDynamicQris(int $idSiswa, int $idTahunAkademik, float $nominal): array
    {
        $siswa = Siswa::findOrFail($idSiswa);
        $cleanNim = preg_replace('/[^0-9]/', '', (string) ($siswa->nisn ?: $siswa->nis ?: $siswa->id));
        $qrisRefNo = 'QRIS-' . date('YmdHis') . '-' . $idSiswa;
        
        $qrisPayload = "00020101021226680016ID.GO.QRIS.WWW01189360091100030012340215" . str_pad($cleanNim, 15, '0', STR_PAD_LEFT)
                     . "520458125303360540" . strlen((string)(int)$nominal) . (int)$nominal
                     . "5802ID5925UNIVERSITAS SIAKAD SPP6007BANDUNG61054011562070703A016304" . strtoupper(bin2hex(random_bytes(2)));

        return [
            'qris_ref_no' => $qrisRefNo,
            'qris_payload' => $qrisPayload,
            'id_siswa' => $idSiswa,
            'id_tahun_akademik' => $idTahunAkademik,
            'nominal' => $nominal,
            'expired_at' => now()->addMinutes(30)->toIso8601String(),
        ];
    }
}
