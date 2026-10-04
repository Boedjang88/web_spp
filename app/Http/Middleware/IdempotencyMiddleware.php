<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Cache duration for Idempotency Keys (24 Hours)
     */
    public const IDEMPOTENCY_TTL_SECONDS = 86400;

    /**
     * Handle an incoming request.
     * Ensure financial requests are idempotent, blocking replays and race conditions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Identify Idempotency Key from Header or Body (Bank TRX ID / Idempotency-Key)
        $idempotencyKey = $request->header('Idempotency-Key')
            ?? $request->input('idempotency_key')
            ?? $request->input('nomor_transaksi_bank');

        if (!$idempotencyKey) {
            return $next($request);
        }

        $cacheKey = 'idempotency_' . md5($request->path() . '_' . $idempotencyKey);
        $lockKey = 'lock_' . $cacheKey;

        // 2. Check if identical request has already been completed & cached
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            return response()->json($cachedResponse['data'], $cachedResponse['status'], array_merge(
                $cachedResponse['headers'] ?? [],
                ['X-Idempotent-Replay' => 'true']
            ));
        }

        // 3. Acquire Atomic Lock to prevent concurrent multi-post race conditions
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sedang diproses secara paralel. Silakan coba kembali beberapa saat lagi.',
                'error_code' => 'CONCURRENT_REQUEST_BLOCKED',
            ], 409);
        }

        try {
            $response = $next($request);

            // 4. Cache successful or validation responses for 24 hours
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                Cache::put($cacheKey, [
                    'data' => $response->getData(true),
                    'status' => $response->getStatusCode(),
                    'headers' => $response->headers->allPreserveCase(),
                ], self::IDEMPOTENCY_TTL_SECONDS);
            }

            return $response;
        } finally {
            $lock->release();
        }
    }
}
