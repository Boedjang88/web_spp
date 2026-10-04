<?php

namespace App\Filament\Widgets;

use App\Services\Monitoring\SystemHealthService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemHealthWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $healthService = app(SystemHealthService::class);
        $health = $healthService->getSystemHealthReport();

        $dbStatus = $health['services']['database']['status'] ?? 'UNKNOWN';
        $dbLatency = $health['services']['database']['latency_ms'] ?? 0;
        $cacheStatus = $health['services']['cache']['status'] ?? 'UNKNOWN';
        $cacheLatency = $health['services']['cache']['latency_ms'] ?? 0;
        $memoryMb = $health['system_metrics']['memory']['current_mb'] ?? 0;
        $memoryLimit = $health['system_metrics']['memory']['limit'] ?? 'N/A';
        $diskUsed = $health['system_metrics']['disk']['used_percentage'] ?? 0;

        return [
            Stat::make('Database Status', $dbStatus === 'OK' ? "Online ({$dbLatency}ms)" : 'Offline / Error')
                ->description("Connection: {$health['services']['database']['connection']}")
                ->descriptionIcon($dbStatus === 'OK' ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($dbStatus === 'OK' ? 'success' : 'danger'),

            Stat::make('Cache / Redis Subsystem', $cacheStatus === 'OK' ? "Online ({$cacheLatency}ms)" : 'Degraded')
                ->description("Driver: {$health['services']['cache']['driver']}")
                ->descriptionIcon($cacheStatus === 'OK' ? 'heroicon-m-bolt' : 'heroicon-m-exclamation-triangle')
                ->color($cacheStatus === 'OK' ? 'info' : 'warning'),

            Stat::make('Memory & Disk Usage', "{$memoryMb} MB (Limit: {$memoryLimit})")
                ->description("Storage Used: {$diskUsed}% of total")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($diskUsed > 85 ? 'danger' : 'primary'),
        ];
    }
}
