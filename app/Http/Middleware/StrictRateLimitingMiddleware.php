<?php

namespace App\Http\Middleware;

use App\Services\Audit\AuditTrailService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class StrictRateLimitingMiddleware
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Handle an incoming request.
     * Enforce strict anti-scraping rate limits and log suspicious scraping in AuditTrailLog.
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 10, int $decayMinutes = 1): Response
    {
        $ip = $request->ip();
        $key = 'strict_rate_' . sha1($ip . '|' . $request->path());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $availableIn = RateLimiter::availableIn($key);

            // Sanitize sensitive input parameters before logging (UU PDP compliance)
            $sanitizedParams = collect($request->except(['_token', 'password', 'password_confirmation']))
                ->map(function ($val, $k) {
                    $lowerK = strtolower($k);
                    if (in_array($lowerK, ['nik', 'no_hp', 'telepon', 'pin', 'token', 'secret'])) {
                        return '***REDACTED***';
                    }
                    if (in_array($lowerK, ['nisn', 'nim', 'nis']) && is_string($val)) {
                        return strlen($val) > 3 ? substr($val, 0, 3) . str_repeat('*', max(0, strlen($val) - 3)) : '***';
                    }
                    return is_string($val) && strlen($val) > 60 ? substr($val, 0, 60) . '...' : $val;
                })->all();

            // Log rate limit violation / anti-scraping block in AuditTrailLog
            $this->auditTrailService->record(
                userId: auth()->id(),
                actionEvent: 'SECURITY_RATE_LIMIT_EXCEEDED',
                payload: [
                    'ip_address' => $ip,
                    'endpoint' => $request->path(),
                    'method' => $request->method(),
                    'input_params' => $sanitizedParams,
                    'user_agent' => $request->userAgent(),
                    'max_allowed_attempts' => $maxAttempts,
                    'retry_after_seconds' => $availableIn,
                    'threat_type' => 'SUSPECTED_DATA_SCRAPING',
                ]
            );

            $headers = [
                'Retry-After' => $availableIn,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ];

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan (Rate limit exceeded). Perlindungan anti-scraping diaktifkan.',
                    'retry_after' => $availableIn,
                ], 429, $headers);
            }

            return redirect()->back()
                ->with('error', "Terlalu banyak permintaan pencarian. Akses dibatasi demi keamanan data (Anti-Scraping Protection). Silakan coba lagi dalam {$availableIn} detik.")
                ->withHeaders($headers);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Append rate limit headers to response
        $remaining = RateLimiter::remaining($key, $maxAttempts);
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));

        return $response;
    }
}
