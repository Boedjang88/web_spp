<?php

namespace App\Services\Monitoring;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class SystemHealthService
{
    /**
     * Run all diagnostic checks and return comprehensive health report
     */
    public function getSystemHealthReport(): array
    {
        $dbHealth = $this->checkDatabase();
        $cacheHealth = $this->checkCache();
        $memoryUsage = $this->getMemoryUsage();
        $diskUsage = $this->getDiskUsage();
        $queueHealth = $this->checkQueue();

        $allOk = ($dbHealth['status'] === 'OK') && ($cacheHealth['status'] === 'OK');

        return [
            'status' => $allOk ? 'HEALTHY' : 'DEGRADED',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'services' => [
                'database' => $dbHealth,
                'cache' => $cacheHealth,
                'queue' => $queueHealth,
            ],
            'system_metrics' => [
                'memory' => $memoryUsage,
                'disk' => $diskUsage,
            ],
        ];
    }

    /**
     * Check Database Connectivity & Latency
     */
    public function checkDatabase(): array
    {
        $startTime = microtime(true);
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => 'OK',
                'latency_ms' => $latencyMs,
                'connection' => DB::getDefaultConnection(),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'DOWN',
                'error' => $e->getMessage(),
                'connection' => DB::getDefaultConnection(),
            ];
        }
    }

    /**
     * Check Cache / Redis Read & Write
     */
    public function checkCache(): array
    {
        $startTime = microtime(true);
        $testKey = 'health_check_probe_' . time();
        try {
            Cache::put($testKey, 'probe_value', 10);
            $readBack = Cache::get($testKey);
            Cache::forget($testKey);

            $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => ($readBack === 'probe_value') ? 'OK' : 'MISMATCH',
                'latency_ms' => $latencyMs,
                'driver' => config('cache.default'),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'DOWN',
                'error' => $e->getMessage(),
                'driver' => config('cache.default'),
            ];
        }
    }

    /**
     * Check Queue status & size
     */
    public function checkQueue(): array
    {
        try {
            return [
                'status' => 'OK',
                'driver' => config('queue.default'),
                'default_queue_size' => Queue::size(),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'UNKNOWN',
                'driver' => config('queue.default'),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get PHP & Server Memory Usage metrics
     */
    public function getMemoryUsage(): array
    {
        $memoryBytes = memory_get_usage(true);
        $peakMemoryBytes = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');

        return [
            'current_mb' => round($memoryBytes / 1024 / 1024, 2),
            'peak_mb' => round($peakMemoryBytes / 1024 / 1024, 2),
            'limit' => $memoryLimit,
        ];
    }

    /**
     * Get Server Disk Space metrics
     */
    public function getDiskUsage(): array
    {
        $totalBytes = @disk_total_space(base_path()) ?: 0;
        $freeBytes = @disk_free_space(base_path()) ?: 0;
        $usedBytes = $totalBytes - $freeBytes;

        return [
            'total_gb' => round($totalBytes / (1024 ** 3), 2),
            'used_gb' => round($usedBytes / (1024 ** 3), 2),
            'free_gb' => round($freeBytes / (1024 ** 3), 2),
            'used_percentage' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0,
        ];
    }
}
