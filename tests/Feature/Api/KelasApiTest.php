<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/kelas');
        $response->assertStatus(401);
    }

    public function test_can_list_all_kelas(): void
    {
        Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        Kelas::create(['nama_kelas' => 'XII TKJ 1', 'kompetensi_keahlian' => 'TKJ']);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/kelas');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');
    }

    public function test_can_create_kelas(): void
    {
        $payload = [
            'nama_kelas' => 'XII DKV 1',
            'kompetensi_keahlian' => 'Desain Komunikasi Visual',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/kelas', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama_kelas', 'XII DKV 1');

        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'XII DKV 1']);
    }

    public function test_can_show_kelas_detail(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/kelas/{$kelas->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.nama_kelas', 'XII RPL 1');
    }

    public function test_can_update_kelas(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/kelas/{$kelas->id}", [
            'nama_kelas' => 'XII RPL 1 Unggulan',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.nama_kelas', 'XII RPL 1 Unggulan');
    }

    public function test_can_delete_kelas(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/kelas/{$kelas->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }
}
