<?php

namespace Tests\Feature\Api;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ActivityLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_cannot_access_activity_logs(): void
    {
        $response = $this->getJson('/api/activity-logs');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_fetch_activity_logs_via_api(): void
    {
        $user = User::factory()->create(['name' => 'API Tester']);
        Sanctum::actingAs($user);

        ActivityLog::record('API_TEST', 'Testing API activity log endpoint.', $user->id);

        $response = $this->getJson('/api/activity-logs');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'action' => 'API_TEST',
            ]);
    }

    public function test_api_login_creates_activity_log(): void
    {
        $user = User::factory()->create([
            'email' => 'api.admin@sekolah.id',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'api.admin@sekolah.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'LOGIN_API',
            'user_id' => $user->id,
        ]);
    }
}
