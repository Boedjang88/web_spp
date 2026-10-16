<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Academic\DiplomaVerificationService;
use App\Services\Audit\AuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiplomaVerificationController extends Controller
{
    public function __construct(
        protected DiplomaVerificationService $verificationService,
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Public Verification of Higher Education Diploma (Ijazah / PIN SIVIL Kemendikbudristek).
     */
    public function verify(Request $request, string $token): \Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
    {
        $sanitizedToken = trim($token);
        $tokenHash = hash('sha256', $sanitizedToken . config('app.key'));

        $verification = $this->verificationService->verifyToken($sanitizedToken);

        // Record public verification audit trail
        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'PUBLIC_DIPLOMA_VERIFICATION',
            payload: [
                'token_hash' => $tokenHash,
                'is_valid' => (bool) $verification,
                'nomor_ijazah_hash' => $verification ? hash('sha256', $verification['nomor_ijazah']) : null,
            ]
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'is_valid' => false,
                    'status' => 'TIDAK_DITEMUKAN',
                    'message' => 'Dokumen ijazah / kelulusan tidak ditemukan dalam basis data nasional (SIVIL / PDDikti) atau token tidak valid.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'is_valid' => true,
                'data' => $verification,
            ], 200);
        }

        if (!$verification) {
            return response()->view('siakad.diploma.verify', [
                'verification' => null,
                'token' => $sanitizedToken,
                'error' => 'Dokumen ijazah / kelulusan tidak ditemukan dalam basis data nasional (SIVIL / PDDikti) atau token verifikasi tidak sah.',
            ], 404);
        }

        return view('siakad.diploma.verify', [
            'verification' => $verification,
            'token' => $sanitizedToken,
        ]);
    }
}
