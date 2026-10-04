<?php

namespace Tests\Feature\Api;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $guru;
    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);

        $guruModel = Guru::create([
            'nip' => '198501152010011002',
            'nama_guru' => 'Budi Santoso, S.Kom.',
            'jenis_kelamin' => 'L',
        ]);

        $siswaModel = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Muhammad Fauzan',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Merdeka No. 1, Bandung',
            'no_telp' => '081234567890',
        ]);

        $this->superadmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@api.com',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin TU',
            'email' => 'admin@api.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->guru = User::factory()->create([
            'name' => 'Guru Budi',
            'email' => 'guru@api.com',
            'role' => 'guru',
            'id_guru' => $guruModel->id,
            'is_active' => true,
        ]);

        $this->siswa = User::factory()->create([
            'name' => 'Siswa Fauzan',
            'email' => 'siswa@api.com',
            'role' => 'siswa',
            'id_siswa' => $siswaModel->id,
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_manage_users_via_api(): void
    {
        $token = $this->superadmin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_create_user_via_api(): void
    {
        $token = $this->admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'API Created User',
                'email' => 'apicreated@test.com',
                'password' => 'secret123',
                'role' => 'guru',
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'apicreated@test.com');
    }

    public function test_guru_cannot_manage_users_via_api(): void
    {
        $token = $this->guru->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_registration_is_blocked(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Hacker Public Register',
            'email' => 'hacker@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    public function test_inactive_user_cannot_login_api(): void
    {
        $inactive = User::factory()->create([
            'email' => 'inactive@api.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@api.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Akun Anda telah dinonaktifkan oleh administrator.');
    }
}
