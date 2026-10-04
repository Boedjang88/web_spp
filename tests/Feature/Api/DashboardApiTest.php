<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_dashboard_summary(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'statistik' => [
                        'total_siswa',
                        'total_kelas',
                        'total_tarif_spp',
                        'total_transaksi',
                        'total_pemasukan',
                        'hari_ini',
                        'bulan_ini',
                    ],
                    'transaksi_terbaru',
                ],
            ]);
    }
}
