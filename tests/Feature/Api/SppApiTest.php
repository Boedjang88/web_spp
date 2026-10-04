<?php

namespace Tests\Feature\Api;

use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_list_all_spp(): void
    {
        Spp::create(['tahun' => 2024, 'nominal' => 250000]);
        Spp::create(['tahun' => 2025, 'nominal' => 300000]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/spp');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');
    }

    public function test_can_create_spp(): void
    {
        $payload = [
            'tahun' => 2026,
            'nominal' => 350000,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/spp', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.tahun', 2026)
            ->assertJsonPath('data.nominal', 350000);

        $this->assertDatabaseHas('spps', ['tahun' => 2026]);
    }

    public function test_can_update_spp(): void
    {
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/spp/{$spp->id}", [
            'nominal' => 325000,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.nominal', 325000);
    }
}
