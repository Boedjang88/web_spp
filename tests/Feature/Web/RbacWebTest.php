<?php

namespace Tests\Feature\Web;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacWebTest extends TestCase
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
            'email' => 'superadmin@test.com',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin TU',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->guru = User::factory()->create([
            'name' => 'Guru Budi',
            'email' => 'guru@test.com',
            'role' => 'guru',
            'id_guru' => $guruModel->id,
            'is_active' => true,
        ]);

        $this->siswa = User::factory()->create([
            'name' => 'Siswa Fauzan',
            'email' => 'siswa@test.com',
            'role' => 'siswa',
            'id_siswa' => $siswaModel->id,
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_access_user_management(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('web.users.index'));
        $response->assertStatus(200)
            ->assertSee('Manajemen Pengguna');
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('web.users.store'), [
            'name' => 'New Teacher User',
            'email' => 'newteacher@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('web.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newteacher@test.com', 'role' => 'guru']);
    }

    public function test_guru_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->guru)->get(route('web.users.index'));
        $response->assertStatus(403);
    }

    public function test_siswa_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('web.users.index'));
        $response->assertStatus(403);
    }

    public function test_guru_can_access_nilai_and_presensi(): void
    {
        $response = $this->actingAs($this->guru)->get(route('web.nilai.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->guru)->get(route('web.presensi.index'));
        $response->assertStatus(200);
    }

    public function test_guru_cannot_access_spp_financial_settings(): void
    {
        $response = $this->actingAs($this->guru)->get(route('web.spp.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->guru)->get(route('web.laporan.index'));
        $response->assertStatus(403);
    }

    public function test_siswa_sees_personalized_student_dashboard(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('dashboard'));
        $response->assertStatus(200)
            ->assertSee('Portal Mandiri Siswa')
            ->assertSee('Muhammad Fauzan');
    }

    public function test_guru_sees_personalized_teacher_dashboard(): void
    {
        $response = $this->actingAs($this->guru)->get(route('dashboard'));
        $response->assertStatus(200)
            ->assertSee('Portal Pengajar / Dewan Guru')
            ->assertSee('Budi Santoso');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::factory()->create([
            'email' => 'blocked@test.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'blocked@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
