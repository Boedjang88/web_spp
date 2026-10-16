<?php

namespace App\Services\Finance;

use App\Models\BankMitra;
use App\Models\FinancialClearance;
use App\Models\Siswa;
use App\Models\TagihanUkt;
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
        ?string $providedSignature = null,
        ?string $clientIp = null
    ): TransaksiH2h {
        return DB::transaction(function () use (
            $nomorVa,
            $nomorTransaksiBank,
            $jumlahBayar,
            $kodeBank,
            $channelBayar,
            $rawPayload,
            $providedSignature,
            $clientIp
        ) {
            // 1. Idempotency Check: Prevent duplicate payment recording
            $existingTx = TransaksiH2h::where('nomor_transaksi_bank', $nomorTransaksiBank)->first();
            if ($existingTx) {
                return $existingTx;
            }

            // 2. Locate Tagihan VA or Tagihan UKT
            $tagihan = $this->resolveTagihanByVaOrInvoice($nomorVa, $jumlahBayar);

            if (!$tagihan) {
                throw new Exception("Virtual Account {$nomorVa} tidak terdaftar di sistem.");
            }

            // 3. Verify HMAC Signature if Bank provides secret key
            if ($tagihan->bankMitra && !empty($tagihan->bankMitra->secret_key)) {
                if (empty($providedSignature)) {
                    throw new Exception('HMAC Security Signature is required by Bank Gateway.');
                }
                $expectedSignature = hash_hmac('sha256', $nomorVa . '|' . $nomorTransaksiBank . '|' . $jumlahBayar, $tagihan->bankMitra->secret_key);
                if (!hash_equals($expectedSignature, $providedSignature)) {
                    throw new Exception('Invalid HMAC Security Signature from Bank Gateway.');
                }
            }

            return $this->reconcilePaymentRecord(
                tagihan: $tagihan,
                nomorTransaksiBank: $nomorTransaksiBank,
                kodeBank: $kodeBank,
                channelBayar: $channelBayar,
                jumlahBayar: $jumlahBayar,
                nomorVa: $nomorVa,
                signatureHash: $providedSignature,
                rawPayload: $rawPayload,
                clientIp: $clientIp
            );
        });
    }

    /**
     * Process Midtrans Payment Gateway Webhook (VA, GoPay, ShopeePay, QRIS, Credit Card)
     *
     * @throws Exception
     */
    public function processMidtransWebhook(array $payload): TransaksiH2h
    {
        return DB::transaction(function () use ($payload) {
            $orderId = $payload['order_id'] ?? null;
            $statusCode = $payload['status_code'] ?? null;
            $grossAmount = $payload['gross_amount'] ?? '0';
            $signatureKey = $payload['signature_key'] ?? null;
            $transactionStatus = $payload['transaction_status'] ?? null;
            $paymentType = $payload['payment_type'] ?? 'midtrans';
            $transactionId = $payload['transaction_id'] ?? ('MID-' . ($orderId ?: uniqid()));

            if (!$orderId) {
                throw new Exception('Order ID is required in Midtrans webhook payload.');
            }

            // Check idempotency
            $existingTx = TransaksiH2h::where('nomor_transaksi_bank', $transactionId)->first();
            if ($existingTx) {
                return $existingTx;
            }

            // Find matching BankMitra configured for Midtrans
            $bankMitra = BankMitra::where('gateway_provider', 'MIDTRANS')
                ->where('is_active', true)
                ->first();

            // Validate SHA512 signature if server key is configured
            if ($bankMitra && !empty($bankMitra->server_key)) {
                if (empty($signatureKey)) {
                    throw new Exception('Midtrans signature_key is required.');
                }
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $bankMitra->server_key);
                if (!hash_equals($expectedSignature, $signatureKey)) {
                    throw new Exception('Invalid Midtrans SHA512 signature key verification failed.');
                }
            }

            // Only reconcile successful settlement / capture payments
            if (!in_array($transactionStatus, ['settlement', 'capture'])) {
                $this->auditService->record(
                    null,
                    'MIDTRANS_PAYMENT_NON_SETTLEMENT',
                    [
                        'order_id' => $orderId,
                        'transaction_status' => $transactionStatus,
                        'payment_type' => $paymentType,
                    ]
                );

                // For pending/cancel, create logged record if needed or return existing
                return TransaksiH2h::firstOrCreate(
                    ['nomor_transaksi_bank' => $transactionId],
                    [
                        'id_tagihan_va' => 1,
                        'kode_bank' => 'MIDTRANS',
                        'nomor_va' => $orderId,
                        'jumlah_dibayar' => (float) $grossAmount,
                        'waktu_transaksi_bank' => now(),
                        'channel_bayar' => $paymentType,
                        'signature_hash' => $signatureKey,
                        'raw_callback_payload' => $payload,
                        'status_callback' => 'SUCCESS',
                    ]
                );
            }

            // Locate Tagihan by Invoice Number or VA Number
            $tagihan = $this->resolveTagihanByVaOrInvoice($orderId, (float) $grossAmount);
            if (!$tagihan) {
                throw new Exception("Tagihan dengan Order ID {$orderId} tidak ditemukan.");
            }

            // Extract specific channel (VA bank or E-Wallet e.g., gopay, shopeepay, qris)
            $channelBayar = match ($paymentType) {
                'bank_transfer' => 'VA_' . strtoupper($payload['va_numbers'][0]['bank'] ?? 'BANK'),
                'echannel' => 'VA_MANDIRI',
                'qris' => 'QRIS',
                'gopay' => 'EWALLET_GOPAY',
                'shopeepay' => 'EWALLET_SHOPEEPAY',
                default => strtoupper($paymentType),
            };

            return $this->reconcilePaymentRecord(
                tagihan: $tagihan,
                nomorTransaksiBank: $transactionId,
                kodeBank: 'MIDTRANS',
                channelBayar: $channelBayar,
                jumlahBayar: (float) $grossAmount,
                nomorVa: $tagihan->nomor_va,
                signatureHash: $signatureKey,
                rawPayload: $payload,
                clientIp: request()->ip() ?? null
            );
        });
    }

    /**
     * Process Xendit Payment Gateway Webhook (Invoice, VA, E-Wallet, QRIS)
     *
     * @throws Exception
     */
    public function processXenditWebhook(array $payload, ?string $callbackToken = null): TransaksiH2h
    {
        return DB::transaction(function () use ($payload, $callbackToken) {
            $externalId = $payload['external_id'] ?? $payload['order_id'] ?? null;
            $amount = (float) ($payload['amount'] ?? $payload['paid_amount'] ?? 0);
            $status = strtoupper($payload['status'] ?? 'PAID');
            $transactionId = $payload['id'] ?? ('XND-' . ($externalId ?: uniqid()));
            $paymentMethod = $payload['payment_method'] ?? $payload['payment_channel'] ?? 'EWALLET';

            if (!$externalId) {
                throw new Exception('External ID is required in Xendit webhook payload.');
            }

            // Check idempotency
            $existingTx = TransaksiH2h::where('nomor_transaksi_bank', $transactionId)->first();
            if ($existingTx) {
                return $existingTx;
            }

            // Verify Xendit Callback Token if configured
            $bankMitra = BankMitra::where('gateway_provider', 'XENDIT')
                ->where('is_active', true)
                ->first();

            if ($bankMitra && !empty($bankMitra->secret_key)) {
                if (empty($callbackToken)) {
                    throw new Exception('Xendit callback verification token is required.');
                }
                if (!hash_equals($bankMitra->secret_key, $callbackToken)) {
                    throw new Exception('Invalid Xendit callback verification token.');
                }
            }

            if (!in_array($status, ['PAID', 'SETTLED', 'COMPLETED', 'SUCCESS'])) {
                throw new Exception("Xendit payment status {$status} is not successful.");
            }

            $tagihan = $this->resolveTagihanByVaOrInvoice($externalId, $amount);
            if (!$tagihan) {
                throw new Exception("Tagihan dengan External ID {$externalId} tidak ditemukan.");
            }

            $channelBayar = 'XENDIT_' . strtoupper($paymentMethod);

            return $this->reconcilePaymentRecord(
                tagihan: $tagihan,
                nomorTransaksiBank: $transactionId,
                kodeBank: 'XENDIT',
                channelBayar: $channelBayar,
                jumlahBayar: $amount,
                nomorVa: $tagihan->nomor_va,
                signatureHash: $callbackToken,
                rawPayload: $payload,
                clientIp: request()->ip() ?? null
            );
        });
    }

    /**
     * Resolve Tagihan either by VA Number, Invoice Number, or synchronizing TagihanUkt.
     */
    protected function resolveTagihanByVaOrInvoice(string $identifier, float $jumlahBayar): ?TagihanVa
    {
        $tagihan = TagihanVa::where('nomor_va', $identifier)
            ->orWhere('nomor_invoice', $identifier)
            ->lockForUpdate()
            ->first();

        if (!$tagihan) {
            $tagihanUkt = TagihanUkt::where('nomor_va', $identifier)
                ->orWhere('nomor_invoice', $identifier)
                ->lockForUpdate()
                ->first();

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

        return $tagihan;
    }

    /**
     * Reconcile payment record, unlock clearances, and log audit entry.
     */
    protected function reconcilePaymentRecord(
        TagihanVa $tagihan,
        string $nomorTransaksiBank,
        string $kodeBank,
        string $channelBayar,
        float $jumlahBayar,
        string $nomorVa,
        ?string $signatureHash,
        array $rawPayload,
        ?string $clientIp = null
    ): TransaksiH2h {
        // Record H2H / Gateway Transaction Ledger
        $transaksi = TransaksiH2h::create([
            'id_tagihan_va' => $tagihan->id,
            'nomor_transaksi_bank' => $nomorTransaksiBank,
            'kode_bank' => $kodeBank,
            'nomor_va' => $nomorVa,
            'jumlah_dibayar' => $jumlahBayar,
            'waktu_transaksi_bank' => now(),
            'channel_bayar' => $channelBayar,
            'signature_hash' => $signatureHash,
            'raw_callback_payload' => $rawPayload,
            'status_callback' => 'SUCCESS',
        ]);

        // Update Invoice State
        $tagihan->total_sudah_bayar += $jumlahBayar;
        if ($tagihan->total_sudah_bayar >= $tagihan->total_harus_bayar) {
            $tagihan->status_pembayaran = 'Lunas';
            $tagihan->tgl_lunas = now();
        } else {
            $tagihan->status_pembayaran = 'Sebagian';
        }
        $tagihan->save();

        // Release KRS Registration Lock instantly
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
                'unlocked_by_channel' => 'GATEWAY_' . $kodeBank . '_' . $channelBayar,
            ]
        );

        // Audit Trail
        $this->auditService->record(
            null,
            'GATEWAY_PAYMENT_SUCCESS',
            [
                'nomor_va' => $nomorVa,
                'nomor_transaksi_bank' => $nomorTransaksiBank,
                'jumlah_bayar' => $jumlahBayar,
                'gateway_provider' => $kodeBank,
                'channel' => $channelBayar,
                'id_siswa' => $tagihan->id_siswa,
                'client_ip' => $clientIp,
                'waktu_transaksi' => now()->toIso8601String(),
            ]
        );

        return $transaksi;
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
