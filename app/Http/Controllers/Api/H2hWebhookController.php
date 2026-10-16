<?php

namespace App\Http\Controllers\Api;

use App\Services\Finance\H2hBillingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class H2hWebhookController extends BaseApiController
{
    public function __construct(
        protected H2hBillingService $h2hService
    ) {}

    /**
     * Real-time Bank Host-to-Host (H2H) Webhook Endpoint
     */
    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_va' => 'required|string',
            'nomor_transaksi_bank' => 'required|string',
            'jumlah_bayar' => 'required|numeric|min:1',
            'kode_bank' => 'required|string',
            'channel_bayar' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        // Timestamp drift verification (Replay attack protection: tolerance max 300 seconds)
        $rawTimestamp = $request->header('X-Timestamp') ?? $request->header('Timestamp') ?? $request->input('timestamp');
        if ($rawTimestamp !== null && $rawTimestamp !== '') {
            $epoch = is_numeric($rawTimestamp) ? (int) $rawTimestamp : strtotime((string) $rawTimestamp);
            if ($epoch !== false && abs(now()->timestamp - $epoch) > 300) {
                \App\Models\ActivityLog::record(
                    'H2H_PAYMENT_REPLAY_ATTACK',
                    "H2H webhook ditolak: Timestamp drift melebihi 300 detik (" . abs(now()->timestamp - $epoch) . "s) dari IP {$request->ip()}."
                );
                return $this->sendError('Timestamp drift melebihi batas toleransi 300 detik. Permintaan ditolak (Anti-Replay Attack).', [], 422);
            }
        }

        try {
            $transaksi = $this->h2hService->processH2hPaymentCallback(
                nomorVa: $validated['nomor_va'],
                nomorTransaksiBank: $validated['nomor_transaksi_bank'],
                jumlahBayar: (float) $validated['jumlah_bayar'],
                kodeBank: $validated['kode_bank'],
                channelBayar: $validated['channel_bayar'] ?? 'ATM',
                rawPayload: $request->all(),
                providedSignature: $validated['signature'] ?? null,
                clientIp: $request->ip()
            );

            \App\Models\ActivityLog::record(
                'H2H_PAYMENT_PROCESSED',
                "Pembayaran H2H berhasil: Bank {$validated['kode_bank']} VA {$transaksi->nomor_va} Rp " . number_format($transaksi->jumlah_dibayar, 0, ',', '.') . " (Ref: {$transaksi->nomor_transaksi_bank}) dari IP {$request->ip()}."
            );

            return $this->sendResponse([
                'nomor_transaksi_bank' => $transaksi->nomor_transaksi_bank,
                'nomor_va' => $transaksi->nomor_va,
                'jumlah_dibayar' => $transaksi->jumlah_dibayar,
                'status' => 'SUCCESS',
                'krs_unlocked' => true,
                'message' => 'Pembayaran H2H berhasil diproses dan blokir KRS dilepas.',
            ], 'Transaksi H2H berhasil diverifikasi.');
        } catch (Exception $e) {
            \App\Models\ActivityLog::record(
                'H2H_PAYMENT_FAILED',
                "Pembayaran H2H gagal untuk VA {$request->input('nomor_va')} (Ref: {$request->input('nomor_transaksi_bank')}) dari IP {$request->ip()}: " . $e->getMessage()
            );
            return $this->sendError($e->getMessage(), [], 422);
        }
    }
}
