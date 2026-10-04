<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySignedDownloadUrl
{
    /**
     * Handle an incoming request for secure signed downloads.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cryptographic Signature & Expiry Check
        if (!$request->hasValidSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'Tautan unduhan tidak valid atau telah kadaluarsa (URL Signature Invalid). Silakan minta tautan baru.',
            ], 403);
        }

        // 2. IDOR Protection (Student Context Guard)
        $user = $request->user();
        if ($user && $user->role === 'siswa' && $user->id_siswa) {
            $requestedSiswaId = $request->route('id_siswa') ?? $request->query('id_siswa') ?? $request->input('id_siswa');

            if ($requestedSiswaId && (int) $requestedSiswaId !== (int) $user->id_siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Percobaan akses IDOR dokumen mahasiswa lain terdeteksi.',
                ], 403);
            }
        }

        return $next($request);
    }
}
