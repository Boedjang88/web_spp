<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and verify user role
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if account is active
        if (isset($user->is_active) && !$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                ], 403);
            }
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
        }

        // Superadmin has access to everything
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Admin has access if role asked includes 'admin' or 'petugas'
        if ($user->role === 'admin' && (empty($roles) || in_array('admin', $roles) || in_array('petugas', $roles))) {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak (403 Forbidden). Role akun Anda (' . $user->role . ') tidak memiliki izin untuk mengakses resource ini.',
                'required_roles' => $roles,
            ], 403);
        }

        abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
