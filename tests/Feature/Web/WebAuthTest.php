<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200)
            ->assertSee('Web SPP Sekolah')
            ->assertSee('Masuk ke Sistem');
    }

    public function test_user_can_login_via_web_form(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@sekolah.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@sekolah.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@sekolah.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@sekolah.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout_via_web(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
