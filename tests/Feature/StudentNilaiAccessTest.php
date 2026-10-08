<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentNilaiAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_web_nilai_page()
    {
        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $siswa = Siswa::create([
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Kampus No 1',
            'no_telp' => '08123456789',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'id_siswa' => $siswa->id,
        ]);

        $response = $this->actingAs($studentUser)->get(route('web.nilai.index'));
        $response->assertStatus(200);
    }
}
