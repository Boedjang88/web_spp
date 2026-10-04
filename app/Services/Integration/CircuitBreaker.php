<?php

namespace App\Services\Integration;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';
    public const STATE_OPEN = 'OPEN';
    public const STATE_HALF_OPEN = 'HALF_OPEN';

    /**
     * Execute an external service call wrapped in a Circuit Breaker pattern.
     *
     * @param string $serviceName Unique identifier for the external service
     * @param callable $action The target network/service call
     * @param callable|null $fallback Optional fallback callback when breaker is OPEN or call fails
     * @param int $failureThreshold Number of consecutive failures before tripping breaker
     * @param int $cooldownSeconds Number of seconds to keep breaker OPEN (default 300 = 5 mins)
     * @return mixed
     * @throws Exception
     */
    public static function call(
        string $serviceName,
        callable $action,
        ?callable $fallback = null,
        int $failureThreshold = 5,
        int $cooldownSeconds = 300
    ): mixed {
        $state = self::getState($serviceName);

        if ($state === self::STATE_OPEN) {
            Log::warning("CircuitBreaker [{$serviceName}]: Breaker is OPEN. Executing fallback or returning circuit open error.");
            if ($fallback !== null) {
                return $fallback(new Exception("Circuit breaker for '{$serviceName}' is OPEN due to repeated failures."));
            }
            throw new Exception("Circuit breaker for '{$serviceName}' is OPEN. Request rejected to protect upstream service.");
        }

        try {
            $result = $action();
            self::recordSuccess($serviceName);
            return $result;
        } catch (\Throwable $e) {
            self::recordFailure($serviceName, $failureThreshold, $cooldownSeconds);
            Log::error("CircuitBreaker [{$serviceName}] Failure: " . $e->getMessage());

            if ($fallback !== null) {
                return $fallback($e);
            }

            throw $e;
        }
    }

    /**
     * Get the current state of the circuit breaker for a service.
     */
    public static function getState(string $serviceName): string
    {
        $openUntil = Cache::get("cb_open_until_{$serviceName}");

        if ($openUntil && now()->timestamp < $openUntil) {
            return self::STATE_OPEN;
        }

        if ($openUntil && now()->timestamp >= $openUntil) {
            return self::STATE_HALF_OPEN;
        }

        return self::STATE_CLOSED;
    }

    /**
     * Record a successful call to the external service.
     */
    public static function recordSuccess(string $serviceName): void
    {
        Cache::forget("cb_failures_{$serviceName}");
        Cache::forget("cb_open_until_{$serviceName}");
    }

    /**
     * Record a failure and potentially trip the circuit breaker.
     */
    public static function recordFailure(string $serviceName, int $failureThreshold = 5, int $cooldownSeconds = 300): void
    {
        $failureKey = "cb_failures_{$serviceName}";
        $failures = (int) Cache::get($failureKey, 0) + 1;
        Cache::put($failureKey, $failures, 3600);

        if ($failures >= $failureThreshold) {
            $openUntil = now()->addSeconds($cooldownSeconds)->timestamp;
            Cache::put("cb_open_until_{$serviceName}", $openUntil, $cooldownSeconds);
            Log::alert("CircuitBreaker [{$serviceName}]: Tripped OPEN for {$cooldownSeconds}s after {$failures} consecutive failures.");
        }
    }

    /**
     * Force reset the circuit breaker to CLOSED.
     */
    public static function reset(string $serviceName): void
    {
        Cache::forget("cb_failures_{$serviceName}");
        Cache::forget("cb_open_until_{$serviceName}");
    }
}
