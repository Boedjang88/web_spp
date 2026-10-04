<?php

namespace Tests\Feature\Web;

use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActivityLogWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_activity_logs(): void
    {
        $response = $this->get('/web/activity-logs');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_activity_logs(): void
    {
        $user = User::factory()->create(['name' => 'Admin Test', 'email' => 'admin@test.id']);
        ActivityLog::record('TEST_ACTION', 'Mencoba log aktivitas pertama.', $user->id);

        $response = $this->actingAs($user)->get('/web/activity-logs');
        $response->assertStatus(200)
            ->assertSee('Log Aktivitas Sistem')
            ->assertSee('TEST_ACTION')
            ->assertSee('Admin Test');
    }

    public function test_user_login_records_activity_log(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@sekolah.id',
            'password' => Hash::make('secret123'),
            'role' => 'petugas',
        ]);

        $this->post('/login', [
            'email' => 'operator@sekolah.id',
            'password' => 'secret123',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'LOGIN',
            'user_id' => $user->id,
        ]);
    }

    public function test_kelas_crud_records_activity_log(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/web/kelas', [
            'nama_kelas' => 'XII RPL 1',
            'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'KELAS_CREATE',
            'user_id' => $user->id,
        ]);
    }
}
