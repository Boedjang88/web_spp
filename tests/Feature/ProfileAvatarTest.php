<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page()
    {
        $user = User::factory()->create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'luayyi@example.com',
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(200);
        $response->assertSee('MUHAMMAD LUAYYI ATHOILLAH');
    }

    public function test_user_can_upload_avatar_photo()
    {
        $user = User::factory()->create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'luayyi@example.com',
            'role' => 'mahasiswa',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH UPDATED',
            'email' => 'luayyi@example.com',
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.index'));
        $this->assertNotNull($user->fresh()->avatar_path);
        $this->assertFileExists(public_path($user->fresh()->avatar_path));

        // Cleanup created file
        if (file_exists(public_path($user->fresh()->avatar_path))) {
            unlink(public_path($user->fresh()->avatar_path));
        }
    }

    public function test_student_can_upload_avatar_via_biodata()
    {
        $kelas = \App\Models\Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'TI']);
        $spp = \App\Models\Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $siswa = Siswa::create([
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Kampus No 1',
            'no_telp' => '08123456789',
        ]);

        $user = User::factory()->create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'luayyi@example.com',
            'role' => 'mahasiswa',
            'id_siswa' => $siswa->id,
        ]);

        $file = UploadedFile::fake()->image('biodata_avatar.png');

        $response = $this->actingAs($user)->put(route('siakad.biodata.update'), [
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH MODIFIED',
            'jenis_kelamin' => 'L',
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $this->assertEquals('MUHAMMAD LUAYYI ATHOILLAH MODIFIED', $user->fresh()->name);
        $this->assertNotNull($user->fresh()->avatar_path);
        $this->assertFileExists(public_path($user->fresh()->avatar_path));

        if (file_exists(public_path($user->fresh()->avatar_path))) {
            unlink(public_path($user->fresh()->avatar_path));
        }
    }
}
