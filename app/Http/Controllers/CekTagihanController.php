<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Services\Audit\AuditTrailService;
use Illuminate\Http\Request;

class CekTagihanController extends Controller
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {}

    public function index()
    {
        return view('cek-tagihan');
    }

    public function search(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:50'
        ], [
            'nisn.required' => 'NIM / NISN mahasiswa wajib diisi.',
        ]);

        $searchKey = trim($request->nisn);
        $ip = $request->ip();
        $failedKey = 'failed_cek_tagihan_' . sha1($ip);

        // Anti-brute force / enumeration protection on repeated invalid lookups
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($failedKey, 5)) {
            $availableIn = \Illuminate\Support\Facades\RateLimiter::availableIn($failedKey);
            $this->auditTrailService->record(
                userId: auth()->id(),
                actionEvent: 'SECURITY_RATE_LIMIT_EXCEEDED',
                payload: [
                    'ip_address' => $ip,
                    'endpoint' => '/cek-tagihan',
                    'threat_type' => 'REPEATED_INVALID_NIM_ENUMERATION',
                    'retry_after_seconds' => $availableIn,
                ]
            );

            $headers = ['Retry-After' => $availableIn];
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan pencarian yang tidak ditemukan. Akses dibatasi demi keamanan data.',
                    'retry_after' => $availableIn,
                ], 429, $headers);
            }

            return redirect()->route('cek.index')
                ->with('error', "Terlalu banyak pencarian tidak ditemukan. Akses dibatasi sementara (Anti-Scraping Protection). Silakan coba lagi dalam {$availableIn} detik.")
                ->withHeaders($headers);
        }

        $siswa = Siswa::with([
            'kelas',
            'spp',
            'pembayarans.petugas',
        ])->where(function ($q) use ($searchKey) {
            $q->where('nisn', $searchKey)
              ->orWhere('nis', $searchKey);
        })->first();

        // Privacy-preserving keyed hash audit logging (UU PDP compliance)
        $keyedHash = hash_hmac('sha256', $searchKey, config('app.key', 'siakad_secret_key'));
        $maskedPreview = strlen($searchKey) > 3 ? substr($searchKey, 0, 3) . str_repeat('*', strlen($searchKey) - 3) : '***';

        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'PUBLIC_PORTAL_CEK_TAGIHAN',
            payload: [
                'search_key_hash' => $keyedHash,
                'search_key_preview' => $maskedPreview,
                'found' => (bool) $siswa,
                'id_siswa' => $siswa?->id,
            ]
        );

        if (!$siswa) {
            \Illuminate\Support\Facades\RateLimiter::hit($failedKey, 600); // 10 minutes decay window
            return redirect()->route('cek.index')->with('error', "Data mahasiswa dengan NIM/NISN '{$searchKey}' tidak ditemukan dalam sistem akademik.");
        }

        // Reset failed counter upon successful legitimate lookup
        \Illuminate\Support\Facades\RateLimiter::clear($failedKey);

        ActivityLog::record('PORTAL_CEK_NISN', "Pencarian data mahasiswa {$siswa->nama} (NIM: {$siswa->nis}) via Portal Layanan Mandiri.");

        return view('cek-tagihan', compact('siswa'));
    }
}