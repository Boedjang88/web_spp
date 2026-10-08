<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEsuratTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_esurat_page()
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($user)->get(route('siakad.esurat.index'));

        $response->assertStatus(200);
        $response->assertViewIs('siakad.student.esurat-index');
    }

    public function test_student_can_submit_esurat_request()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $siswa = Siswa::create([
            'nisn' => '1234567890',
            'nis' => '12345',
            'nama' => 'Mahasiswa Esurat',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Merdeka No. 10',
            'no_telp' => '08123456789',
            'status_kelulusan' => 'Aktif',
        ]);
        $user->update(['id_siswa' => $siswa->id]);

        $response = $this->actingAs($user)->post(route('siakad.esurat.store'), [
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'perihal' => 'Surat Aktif Kuliah',
            'keperluan' => 'Persyaratan Beasiswa',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('surat_akademiks', [
            'id_siswa' => $siswa->id,
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
        ]);
    }
}
