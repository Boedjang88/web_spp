<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleSsoTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_sso_redirect_flow()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
        ]);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_sso_callback_flow()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
        ]);

        $response = $this->get(route('auth.google.callback', ['code' => 'sample_google_code']));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('google', $user->fresh()->sso_provider);
    }
}
