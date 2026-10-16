<?php

namespace App\Http\Controllers\Api;

use App\Services\Finance\H2hBillingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayWebhookController extends BaseApiController
{
    public function __construct(
        protected H2hBillingService $billingService
    ) {}

    /**
     * Handle Midtrans Payment Gateway Webhook Callback
     */
    public function handleMidtrans(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Timestamp drift verification (Replay attack protection: tolerance max 300 seconds)
        $rawTimestamp = $request->header('X-Timestamp') ?? $request->header('Timestamp') ?? $request->input('timestamp');
        if ($rawTimestamp !== null && $rawTimestamp !== '') {
            $epoch = is_numeric($rawTimestamp) ? (int) $rawTimestamp : strtotime((string) $rawTimestamp);
            if ($epoch !== false && abs(now()->timestamp - $epoch) > 300) {
                \App\Models\ActivityLog::record(
                    'MIDTRANS_REPLAY_ATTACK',
                    "Midtrans webhook ditolak: Timestamp drift melebihi 300 detik (" . abs(now()->timestamp - $epoch) . "s) dari IP {$request->ip()}."
                );
                return $this->sendError('Timestamp drift melebihi batas toleransi 300 detik. Permintaan ditolak (Anti-Replay Attack).', [], 422);
            }
        }

        try {
            $transaksi = $this->billingService->processMidtransWebhook($payload);

            \App\Models\ActivityLog::record(
                'MIDTRANS_WEBHOOK_PROCESSED',
                "Midtrans webhook diproses: Order {$transaksi->nomor_va} (Ref: {$transaksi->nomor_transaksi_bank}) Rp " . number_format($transaksi->jumlah_dibayar, 0, ',', '.') . " Channel: {$transaksi->channel_bayar} dari IP {$request->ip()}."
            );

            return $this->sendResponse([
                'transaction_id' => $transaksi->nomor_transaksi_bank,
                'order_id' => $transaksi->nomor_va,
                'status' => $transaksi->status_callback,
                'amount' => $transaksi->jumlah_dibayar,
                'channel' => $transaksi->channel_bayar,
                'reconciled' => true,
            ], 'Midtrans payment reconciled successfully.');
        } catch (Exception $e) {
            \App\Models\ActivityLog::record(
                'MIDTRANS_WEBHOOK_FAILED',
                "Midtrans webhook gagal dari IP {$request->ip()}: " . $e->getMessage()
            );
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Handle Xendit Payment Gateway Webhook Callback
     */
    public function handleXendit(Request $request): JsonResponse
    {
        $payload = $request->all();
        $callbackToken = $request->header('x-callback-token') ?? $request->header('X-CALLBACK-TOKEN');

        // Timestamp drift verification (Replay attack protection: tolerance max 300 seconds)
        $rawTimestamp = $request->header('X-Timestamp') ?? $request->header('Timestamp') ?? $request->input('timestamp');
        if ($rawTimestamp !== null && $rawTimestamp !== '') {
            $epoch = is_numeric($rawTimestamp) ? (int) $rawTimestamp : strtotime((string) $rawTimestamp);
            if ($epoch !== false && abs(now()->timestamp - $epoch) > 300) {
                \App\Models\ActivityLog::record(
                    'XENDIT_REPLAY_ATTACK',
                    "Xendit webhook ditolak: Timestamp drift melebihi 300 detik (" . abs(now()->timestamp - $epoch) . "s) dari IP {$request->ip()}."
                );
                return $this->sendError('Timestamp drift melebihi batas toleransi 300 detik. Permintaan ditolak (Anti-Replay Attack).', [], 422);
            }
        }

        try {
            $transaksi = $this->billingService->processXenditWebhook($payload, $callbackToken);

            \App\Models\ActivityLog::record(
                'XENDIT_WEBHOOK_PROCESSED',
                "Xendit webhook diproses: External ID {$transaksi->nomor_va} (Ref: {$transaksi->nomor_transaksi_bank}) Rp " . number_format($transaksi->jumlah_dibayar, 0, ',', '.') . " Channel: {$transaksi->channel_bayar} dari IP {$request->ip()}."
            );

            return $this->sendResponse([
                'transaction_id' => $transaksi->nomor_transaksi_bank,
                'external_id' => $transaksi->nomor_va,
                'status' => $transaksi->status_callback,
                'amount' => $transaksi->jumlah_dibayar,
                'channel' => $transaksi->channel_bayar,
                'reconciled' => true,
            ], 'Xendit payment reconciled successfully.');
        } catch (Exception $e) {
            \App\Models\ActivityLog::record(
                'XENDIT_WEBHOOK_FAILED',
                "Xendit webhook gagal dari IP {$request->ip()}: " . $e->getMessage()
            );
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Unified payment gateway webhook router (auto-detect provider)
     */
    public function handleUnified(Request $request): JsonResponse
    {
        // Detect Midtrans payload
        if ($request->has('order_id') && ($request->has('transaction_status') || $request->has('signature_key'))) {
            return $this->handleMidtrans($request);
        }

        // Detect Xendit payload
        if ($request->has('external_id') || $request->header('x-callback-token')) {
            return $this->handleXendit($request);
        }

        return $this->sendError('Unrecognized payment gateway payload format.', [], 400);
    }
}
