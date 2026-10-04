<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'API User',
            'email' => 'apiuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_view_profile_api(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'apiuser@test.com');
    }

    public function test_user_can_update_profile_api(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/auth/profile', [
            'name' => 'API User Updated',
            'email' => 'updatedapi@test.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'API User Updated')
            ->assertJsonPath('data.email', 'updatedapi@test.com');

        $this->user->refresh();
        $this->assertEquals('API User Updated', $this->user->name);
    }

    public function test_user_can_change_password_api(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'password123',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->user->refresh();
        $this->assertTrue(Hash::check('newsecretpassword', $this->user->password));
    }

    public function test_user_fails_change_password_with_incorrect_current_password(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/auth/change-password', [
            'current_password' => 'wrongcurrent',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }
}
