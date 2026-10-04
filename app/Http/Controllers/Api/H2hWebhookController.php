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

        try {
            $transaksi = $this->h2hService->processH2hPaymentCallback(
                nomorVa: $validated['nomor_va'],
                nomorTransaksiBank: $validated['nomor_transaksi_bank'],
                jumlahBayar: (float) $validated['jumlah_bayar'],
                kodeBank: $validated['kode_bank'],
                channelBayar: $validated['channel_bayar'] ?? 'ATM',
                rawPayload: $request->all(),
                providedSignature: $validated['signature'] ?? null
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
            return $this->sendError($e->getMessage(), [], 422);
        }
    }
}
