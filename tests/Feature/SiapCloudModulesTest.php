<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\SuratAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiapCloudModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $adminUser;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $this->siswa = Siswa::create([
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. SIAP Cloud No. 1',
            'no_telp' => '081234567890',
            'status_kelulusan' => 'Aktif',
        ]);

        $this->studentUser = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'id_siswa' => $this->siswa->id,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin BAAK',
        ]);
    }

    public function test_esurat_requires_admin_approval_and_cannot_be_immediately_approved()
    {
        $response = $this->actingAs($this->studentUser)->post(route('siakad.esurat.store'), [
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'perihal' => 'Pengajuan SKMA',
            'keperluan' => 'Persyaratan Beasiswa',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_akademiks', [
            'id_siswa' => $this->siswa->id,
            'status' => 'DRAFT',
        ]);

        $surat = SuratAkademik::where('id_siswa', $this->siswa->id)->first();

        // Admin approves e-Surat
        $approveResponse = $this->actingAs($this->adminUser)->post(route('siakad.baak.esurat.approve', $surat->id));
        $approveResponse->assertRedirect();
        $this->assertEquals('DISETUJUI', $surat->fresh()->status);
    }

    public function test_kkn_module_registration()
    {
        $response = $this->actingAs($this->studentUser)->get(route('siakad.kkn.index'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->studentUser)->post(route('siakad.kkn.register'), [
            'lokasi_kkn' => 'Desa Ciawi',
            'kelompok' => 'Kelompok 05',
            'dpl_name' => 'Dr. DPL',
        ]);
        $postResponse->assertRedirect();
        $this->assertDatabaseHas('kkn_registrasis', [
            'id_siswa' => $this->siswa->id,
            'desa_lokasi' => 'Desa Ciawi',
        ]);
    }

    public function test_wisuda_clearance_module()
    {
        $response = $this->actingAs($this->studentUser)->get(route('siakad.wisuda.index'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->studentUser)->post(route('siakad.wisuda.register'));
        $postResponse->assertRedirect();
        $this->assertDatabaseHas('siswas', [
            'id' => $this->siswa->id,
            'status_kelulusan' => 'Lulus Wisuda',
        ]);
    }

    public function test_dbpta_proposal_bimbingan_sidang_pemberkasan_modules()
    {
        // 1. Proposal
        $this->actingAs($this->studentUser)->get(route('siakad.dbpta.proposal.index'))->assertStatus(200);
        $this->actingAs($this->studentUser)->post(route('siakad.dbpta.proposal.store'), [
            'judul' => 'Sistem Informasi Akademik Enterprise SIAP 5.6',
            'abstrak' => 'Abstrak penelitian skripsi SIAP 5.6 Cloud...',
        ])->assertRedirect();

        // 2. Bimbingan Logbook
        $this->actingAs($this->studentUser)->get(route('siakad.dbpta.bimbingan.index'))->assertStatus(200);
        $this->actingAs($this->studentUser)->post(route('siakad.dbpta.bimbingan.store'), [
            'bab' => 'BAB I - Pendahuluan',
            'materi_bimbingan' => 'Pembahasan Latar Belakang Masalah',
            'saran_dosen' => 'Lanjutkan ke Bab II',
        ])->assertRedirect();

        // 3. Sidang Yudisium
        $this->actingAs($this->studentUser)->get(route('siakad.dbpta.sidang.index'))->assertStatus(200);
        $this->actingAs($this->studentUser)->post(route('siakad.dbpta.sidang.store'))->assertRedirect();

        // 4. Pemberkasan
        $this->actingAs($this->studentUser)->get(route('siakad.dbpta.pemberkasan.index'))->assertStatus(200);
        $this->actingAs($this->studentUser)->post(route('siakad.dbpta.pemberkasan.store'), [
            'link_berkas' => 'https://drive.google.com/file/d/sample',
        ])->assertRedirect();
    }
}
