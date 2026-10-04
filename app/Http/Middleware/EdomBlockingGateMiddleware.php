<?php

namespace App\Http\Middleware;

use App\Models\KrsDetail;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EdomBlockingGateMiddleware
{
    /**
     * Handle an incoming request.
     * Block KHS/Rapor route if student has pending lecturer evaluations (EDOM)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'siswa' && $user->id_siswa) {
            // Check if there are any published course grades that lack EDOM evaluation
            $pendingEdom = KrsDetail::whereHas('krs', function ($q) use ($user) {
                $q->where('id_siswa', $user->id_siswa);
            })
            ->where('is_published', true)
            ->doesntHave('edomEvaluasi')
            ->exists();

            if ($pendingEdom) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses KHS / Kartu Hasil Studi Terkunci! Anda diwajibkan mengisi Kuesioner Evaluasi Dosen (EDOM) terlebih dahulu.',
                        'is_edom_blocked' => true,
                    ], 403);
                }

                return redirect()->route('dashboard')->with('error', 'Akses KHS / Kartu Hasil Studi Terkunci! Harap selesaikan pengisian Kuesioner Evaluasi Dosen (EDOM) terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
