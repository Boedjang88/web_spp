<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfaVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->mfa_enabled && !$request->session()->get('mfa_verified', false)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Two-Factor Authentication (MFA) verification required.',
                    'mfa_required' => true,
                ], 403);
            }

            return redirect()->route('mfa.verify.show')->with('error', 'Silakan verifikasi kode 2FA/OTP terlebih dahulu.');
        }

        return $next($request);
    }
}
