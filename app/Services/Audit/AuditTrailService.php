<?php

namespace App\Services\Audit;

use App\Models\AuditTrailLog;

class AuditTrailService
{
    /**
     * Record an immutable audit log entry with microsecond precision
     */
    public function record(
        ?int $userId,
        string $actionEvent,
        ?array $payload = null,
        ?array $stateBefore = null,
        ?array $stateAfter = null
    ): AuditTrailLog {
        $microtime = sprintf('%.6f', microtime(true));

        return AuditTrailLog::create([
            'id_user' => $userId ?? auth()->id(),
            'action_event' => $actionEvent,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'url_endpoint' => request()?->fullUrl(),
            'request_payload' => $payload ?? request()?->all(),
            'state_before' => $stateBefore,
            'state_after' => $stateAfter,
            'timestamp_microseconds' => $microtime,
        ]);
    }
}
