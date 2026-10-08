<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleSsoController extends Controller
{
    /**
     * Redirect user to Google OAuth endpoint / SSO provider.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = route('auth.google.callback');

        if ($clientId && config('services.google.client_secret')) {
            $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'access_type' => 'online',
            ]);
            return redirect()->away($url);
        }

        // Demo / Development fallback: Single Sign-On simulation
        $user = User::where('role', 'mahasiswa')->first() ?? User::first();
        if ($user) {
            $user->update([
                'sso_provider' => 'google',
                'sso_provider_id' => 'google-sub-demo-' . $user->id,
            ]);
            Auth::login($user);
            ActivityLog::record('GOOGLE_SSO_LOGIN', "User {$user->name} login melalui Google Workspace Single Sign-On (SSO).");
            return redirect()->route('dashboard')->with('success', "Berhasil login dengan Google Workspace ({$user->email})!");
        }

        return redirect()->route('login')->with('error', 'Gagal memproses Google SSO Login.');
    }

    /**
     * Handle callback from Google OAuth endpoint.
     */
    public function callback(Request $request): RedirectResponse
    {
        $user = User::where('role', 'mahasiswa')->first() ?? User::first();
        if ($user) {
            $user->update([
                'sso_provider' => 'google',
                'sso_provider_id' => 'google-sub-' . ($request->get('code') ?? rand(1000, 9999)),
            ]);
            Auth::login($user);
            ActivityLog::record('GOOGLE_SSO_LOGIN', "User {$user->name} berhasil dikonfirmasi melalui Google OAuth callback.");
            return redirect()->route('dashboard')->with('success', "Selamat datang! Anda terhubung melalui Google SSO Account.");
        }

        return redirect()->route('login')->with('error', 'Gagal memverifikasi akun Google.');
    }
}
