<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PdpConsentController extends Controller
{
    /**
     * Display UU PDP Consent & Privacy Agreement Screen
     */
    public function show(): View
    {
        return view('auth.pdp-consent');
    }

    /**
     * Store User's Consent Acceptance Timestamp and IP
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'agree_pdp' => 'required|accepted',
        ], [
            'agree_pdp.accepted' => 'Anda wajib menyetujui lembar persetujuan pemrosesan data pribadi untuk melanjutkan.',
        ]);

        $user = $request->user();
        $now = now();
        $ip = $request->ip();

        $user->update([
            'consent_pdp_at' => $now,
            'consent_pdp_ip' => $ip,
        ]);

        if ($user->siswa) {
            $user->siswa->update([
                'consent_pdp_at' => $now,
                'consent_pdp_ip' => $ip,
            ]);
        }

        if ($user->guru) {
            $user->guru->update([
                'consent_pdp_at' => $now,
                'consent_pdp_ip' => $ip,
            ]);
        }

        ActivityLog::record('UU_PDP_CONSENT_SIGNED', "User {$user->name} menyetujui pemrosesan data pribadi UU PDP.");

        return redirect()->route('dashboard')->with('success', 'Persetujuan digital pemrosesan data pribadi berhasil diverifikasi.');
    }
}
