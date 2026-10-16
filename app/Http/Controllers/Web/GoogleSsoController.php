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
        $clientSecret = config('services.google.client_secret');
        $redirectUri = route('auth.google.callback');

        if (!empty($clientId) && !empty($clientSecret)) {
            $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'access_type' => 'online',
            ]);
            return redirect()->away($url);
        }

        // Secure handling: Do NOT fall back to Superadmin or Student #1.
        return redirect()->route('login')->with('error', 'Layanan Google SSO belum dikonfigurasi pada server ini.');
    }

    /**
     * Handle callback from Google OAuth endpoint.
     */
    public function callback(Request $request): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $code = $request->get('code');

        if (empty($clientId) || empty($clientSecret) || empty($code)) {
            return redirect()->route('login')->with('error', 'Autentikasi Google SSO tidak valid atau belum dikonfigurasi.');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => route('auth.google.callback'),
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                return redirect()->route('login')->with('error', 'Gagal memperoleh token verifikasi dari Google.');
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')->with('error', 'Token akses Google tidak valid.');
            }

            $userinfoResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$userinfoResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal memverifikasi identitas pengguna dari Google.');
            }

            $googleUser = $userinfoResponse->json();
            $email = $googleUser['email'] ?? null;
            $googleId = $googleUser['sub'] ?? null;

            if (!$email) {
                return redirect()->route('login')->with('error', 'Email Google tidak ditemukan.');
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->route('login')->with('error', "Akun Google ({$email}) tidak terdaftar dalam sistem SIAKAD.");
            }

            $user->update([
                'sso_provider' => 'google',
                'sso_provider_id' => $googleId,
            ]);

            Auth::login($user);
            ActivityLog::record('GOOGLE_SSO_LOGIN', "User {$user->name} berhasil login melalui Google Workspace SSO.");

            return redirect()->route('dashboard')->with('success', "Selamat datang! Berhasil terhubung melalui Google Workspace ({$user->email}).");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google SSO Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat memproses Google SSO.');
        }
    }
}
