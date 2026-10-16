<?php

namespace Tests\Feature\Academic;

use App\Models\Fakultas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\LogbookBimbingan;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedModulesWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $dosenUser;
    protected Siswa $siswa;
    protected Guru $guru;
    protected TahunAkademik $tahunAkademik;
    protected Kurikulum $kurikulum;

    protected function setUp(): void
    {
        parent::setUp();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $prodi->id,
            'kode_kurikulum' => 'KUR-2026',
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::firstOrCreate(
            ['kode_tahun' => '20261'],
            [
                'nama_tahun' => '2026/2027 Ganjil',
                'semester' => 'Ganjil',
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'is_active' => true,
            ]
        );

        $kelas = Kelas::create(['nama_kelas' => 'IF-4A', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        $this->siswa = Siswa::create([
            'nisn' => '109240940088',
            'nis' => '109240940088',
            'nama' => 'Bintang Mahasiswa',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Boulevard No. 1',
            'no_telp' => '081234567888',
            'status_kelulusan' => 'Aktif',
        ]);

        $this->studentUser = User::factory()->create([
            'name' => 'Bintang Mahasiswa',
            'email' => 'bintang@siakad.ac.id',
            'role' => 'mahasiswa',
            'id_siswa' => $this->siswa->id,
        ]);

        $this->guru = Guru::create([
            'nip' => '198501012010011001',
            'nama_guru' => 'Prof. Dr. Hendra Gunawan',
            'jenis_kelamin' => 'L',
        ]);

        $this->dosenUser = User::factory()->create([
            'name' => 'Prof. Dr. Hendra Gunawan',
            'email' => 'hendra@siakad.ac.id',
            'role' => 'guru',
            'id_guru' => $this->guru->id,
        ]);
    }

    protected function seedPassedCredits(int $sksAmount): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'MK-CREDIT-' . $sksAmount . '-' . uniqid(),
            'nama_mk' => 'Paket SKS ' . $sksAmount,
            'sks_teori' => $sksAmount,
            'sks_praktik' => 0,
            'sks_total' => $sksAmount,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'KLAS-' . uniqid(),
            'kuota_maksimal' => 50,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'status_krs' => 'Disetujui',
        ]);

        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'nilai_akhir_huruf' => 'A',
            'bobot_mutu' => 4.0,
            'is_lulus' => true,
        ]);
    }

    public function test_kkn_registration_rejects_student_with_less_than_100_sks(): void
    {
        // Student has only 60 SKS (< 100 SKS threshold)
        $this->seedPassedCredits(60);

        $response = $this->actingAs($this->studentUser)->post(route('siakad.kkn.register'), [
            'lokasi_kkn' => 'Desa Sukamaju, Ciawi',
            'kelompok' => 'Kelompok KKN-01',
            'dpl_name' => 'Dr. DPL',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('kkn_registrasis', [
            'id_siswa' => $this->siswa->id,
        ]);
    }

    public function test_kkn_registration_succeeds_when_student_has_at_least_100_sks(): void
    {
        // Student fulfills 100 SKS requirement
        $this->seedPassedCredits(105);

        $response = $this->actingAs($this->studentUser)->post(route('siakad.kkn.register'), [
            'lokasi_kkn' => 'Desa Sukamaju, Ciawi',
            'kelompok' => 'Kelompok KKN-01',
            'dpl_name' => 'Dr. DPL',
        ]);

        $response->assertRedirect(route('siakad.kkn.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kkn_registrasis', [
            'id_siswa' => $this->siswa->id,
            'desa_lokasi' => 'Desa Sukamaju, Ciawi',
        ]);
    }

    public function test_dbpta_proposal_rejects_student_with_less_than_110_sks(): void
    {
        // Student has only 95 SKS (< 110 SKS threshold)
        $this->seedPassedCredits(95);

        $response = $this->actingAs($this->studentUser)->post(route('siakad.dbpta.proposal.store'), [
            'judul' => 'Analisis Performa Sistem SIAKAD',
            'abstrak' => 'Penelitian ini menganalisis performa arsitektur SIAKAD...',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('tugas_akhirs', [
            'id_siswa' => $this->siswa->id,
        ]);
    }

    public function test_dbpta_proposal_succeeds_when_student_has_at_least_110_sks(): void
    {
        // Student has 120 SKS (>= 110 SKS threshold)
        $this->seedPassedCredits(120);

        $response = $this->actingAs($this->studentUser)->post(route('siakad.dbpta.proposal.store'), [
            'judul' => 'Analisis Keamanan Smart KRS dan Sistem Pembayaran Terintegrasi',
            'abstrak' => 'Penelitian ini membahas pengamanan HMAC SHA256 dan Anti-IDOR pada sistem SIAKAD...',
        ]);

        $response->assertRedirect(route('siakad.dbpta.proposal.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tugas_akhirs', [
            'id_siswa' => $this->siswa->id,
            'status_skripsi' => 'Pengajuan Proposal',
        ]);
    }

    public function test_dbpta_bimbingan_logbook_created_with_pending_status_and_not_auto_approved(): void
    {
        $tugasAkhir = TugasAkhir::create([
            'id_siswa' => $this->siswa->id,
            'judul' => 'Skripsi Uji Coba Logbook',
            'abstrak' => 'Abstrak skripsi uji coba logbook...',
            'status_skripsi' => 'Bimbingan',
        ]);

        $response = $this->actingAs($this->studentUser)->post(route('siakad.dbpta.bimbingan.store'), [
            'bab' => 'BAB 1',
            'materi_bimbingan' => 'Konsultasi rumusan masalah dan batasan masalah',
            'saran_dosen' => 'Perbaiki fokus penelitian',
        ]);

        $response->assertRedirect(route('siakad.dbpta.bimbingan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('logbook_bimbingans', [
            'id_tugas_akhir' => $tugasAkhir->id,
            'status_acc' => 'Pending',
        ]);

        $logbook = LogbookBimbingan::where('id_tugas_akhir', $tugasAkhir->id)->first();
        $this->assertEquals('BAB 1', $logbook->bab);
        $this->assertStringContainsString('Konsultasi rumusan masalah', $logbook->materi_bimbingan);
    }

    public function test_lecturer_can_approve_dbpta_bimbingan_logbook(): void
    {
        $tugasAkhir = TugasAkhir::create([
            'id_siswa' => $this->siswa->id,
            'judul' => 'Skripsi Logbook Approval',
            'abstrak' => 'Abstrak...',
            'status_skripsi' => 'Bimbingan',
        ]);

        $logbook = LogbookBimbingan::create([
            'id_tugas_akhir' => $tugasAkhir->id,
            'id_guru' => $this->guru->id,
            'tanggal_bimbingan' => now(),
            'catatan_kemajuan_mahasiswa' => '[BAB 2] Tinjauan Pustaka & Landasan Teori',
            'status_acc' => 'Pending',
        ]);

        $response = $this->actingAs($this->dosenUser)->post(route('siakad.dbpta.bimbingan.approve', $logbook->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('Disetujui', $logbook->fresh()->status_acc);
        $this->assertNotNull($logbook->fresh()->tgl_disetujui);
    }
}
