<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleSsoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Unconfigured Google SSO safely redirects to login without auto-logging in as admin or student.
     */
    public function test_google_sso_fails_safely_when_unconfigured(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
        ]);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test: Configured Google SSO redirects away to Google OAuth authorization URL.
     */
    public function test_google_sso_redirects_to_google_when_configured(): void
    {
        config([
            'services.google.client_id' => 'sample_client_id_123',
            'services.google.client_secret' => 'sample_client_secret_xyz',
        ]);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
        $this->assertStringContainsString('client_id=sample_client_id_123', $response->getTargetUrl());
        $this->assertGuest();
    }

    /**
     * Test: Valid Google OAuth callback authenticates the matching student.
     */
    public function test_google_sso_callback_authenticates_matching_user(): void
    {
        config([
            'services.google.client_id' => 'sample_client_id_123',
            'services.google.client_secret' => 'sample_client_secret_xyz',
        ]);

        $user = User::factory()->create([
            'email' => 'athoil@siakad.ac.id',
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock_google_access_token_12345',
                'expires_in' => 3600,
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'google_sub_id_99999',
                'email' => 'athoil@siakad.ac.id',
                'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            ], 200),
        ]);

        $response = $this->get(route('auth.google.callback', ['code' => 'valid_sample_code']));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('google', $user->fresh()->sso_provider);
        $this->assertEquals('google_sub_id_99999', $user->fresh()->sso_provider_id);
    }
}
