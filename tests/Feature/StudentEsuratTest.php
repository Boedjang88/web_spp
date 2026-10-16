<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\SuratAkademik;
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
            'status' => 'MENUNGGU_PERSETUJUAN',
        ]);
    }

    public function test_multiple_esurat_submissions_generate_unique_nomor_surat()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $siswa = Siswa::create([
            'nisn' => '9876543210',
            'nis' => '54321',
            'nama' => 'Mahasiswa Unique Test',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Merdeka No. 10',
            'no_telp' => '08123456789',
            'status_kelulusan' => 'Aktif',
        ]);
        $user->update(['id_siswa' => $siswa->id]);

        // Pre-create a record with a potential colliding nomor_surat
        $nextNumber = 'SKMA/' . date('Y/m/') . '0001';
        SuratAkademik::create([
            'id_siswa' => $siswa->id,
            'jenis_surat' => 'Pre-existing',
            'nomor_surat' => $nextNumber,
            'perihal' => 'Existing',
            'keperluan' => 'Existing',
            'qr_verification_token' => 'token1',
            'file_pdf_path' => 'doc1.pdf',
            'status' => 'DISETUJUI',
            'tgl_terbit' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('siakad.esurat.store'), [
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'perihal' => 'Surat Aktif Kuliah 2',
            'keperluan' => 'Persyaratan Beasiswa 2',
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, SuratAkademik::count());
        $this->assertDatabaseHas('surat_akademiks', [
            'nomor_surat' => 'SKMA/' . date('Y/m/') . '0002',
            'status' => 'MENUNGGU_PERSETUJUAN',
        ]);
    }

    public function test_baak_admin_can_view_pending_esurat_and_approve_or_reject()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentUser = User::factory()->create(['role' => 'siswa']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-2026-B', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $siswa = Siswa::create([
            'nisn' => '1122334455',
            'nis' => '11223',
            'nama' => 'Mahasiswa Approval Test',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Kampus No. 1',
            'no_telp' => '081234567890',
            'status_kelulusan' => 'Aktif',
        ]);
        $studentUser->update(['id_siswa' => $siswa->id]);

        $surat = SuratAkademik::create([
            'id_siswa' => $siswa->id,
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'nomor_surat' => 'SKMA/' . date('Y/m/') . '9999',
            'perihal' => 'Surat Aktif Kuliah',
            'keperluan' => 'Persyaratan Beasiswa',
            'qr_verification_token' => 'token-test',
            'file_pdf_path' => 'doc.pdf',
            'status' => 'MENUNGGU_PERSETUJUAN',
            'tgl_terbit' => now(),
        ]);

        // Admin views pending list
        $viewResponse = $this->actingAs($admin)->get(route('siakad.baak.esurat.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($surat->nomor_surat);

        // Admin approves
        $approveResponse = $this->actingAs($admin)->post(route('siakad.baak.esurat.approve', $surat->id));
        $approveResponse->assertRedirect();
        $this->assertEquals('DISETUJUI', $surat->fresh()->status);

        // Admin rejects
        $rejectResponse = $this->actingAs($admin)->post(route('siakad.baak.esurat.reject', $surat->id));
        $rejectResponse->assertRedirect();
        $this->assertEquals('DITOLAK', $surat->fresh()->status);
    }
}
