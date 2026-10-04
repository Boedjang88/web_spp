<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UuPdpConsentMiddleware
{
    /**
     * Handle an incoming request.
     * Enforce UU PDP Digital Privacy & Data Processing Consent
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Whitelist consent route and logout from interception
            $currentRoute = $request->route()?->getName();
            if (in_array($currentRoute, ['pdp.consent.show', 'pdp.consent.store', 'logout', 'api.auth.logout'])) {
                return $next($request);
            }

            // Check if user has consented
            if (is_null($user->consent_pdp_at)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Persetujuan Pemrosesan Data Pribadi (UU PDP No. 27/2022) Diperlukan.',
                        'requires_pdp_consent' => true,
                    ], 403);
                }

                return redirect()->route('pdp.consent.show');
            }
        }

        return $next($request);
    }
}
