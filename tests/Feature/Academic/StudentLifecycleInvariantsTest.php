<?php

namespace Tests\Feature\Academic;

use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\MataKuliah;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\Academic\GraduationClearanceService;
use App\Services\Academic\SmartKrsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLifecycleInvariantsTest extends TestCase
{
    use RefreshDatabase;

    private Siswa $activeStudent;
    private Siswa $graduatedStudent;
    private TahunAkademik $tahun;
    private KelasKuliah $kelasKuliah;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create([
            'nama_kelas' => 'TI-4A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 5000000,
        ]);

        $this->activeStudent = Siswa::create([
            'nisn' => '2026001001',
            'nis' => '1001',
            'nama' => 'Mahasiswa Aktif Reguler',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Kampus No. 1',
            'no_telp' => '081234567891',
            'status_kelulusan' => 'Aktif',
        ]);

        $this->graduatedStudent = Siswa::create([
            'nisn' => '2026002002',
            'nis' => '2002',
            'nama' => 'Alumni Wisudawan',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Alumni No. 2',
            'no_telp' => '081234567892',
            'status_kelulusan' => 'Lulus Wisuda',
            'tgl_kelulusan' => '2026-08-20',
            'nomor_ijazah' => 'PIN/2026/000202',
        ]);

        $fakultas = \App\Models\Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $prodi = \App\Models\ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $this->kurikulum = \App\Models\Kurikulum::create([
            'id_prodi' => $prodi->id,
            'kode_kurikulum' => 'KUR-2026',
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->tahun = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF301',
            'nama_mk' => 'Rekayasa Perangkat Lunak',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $this->kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahun->id,
            'nama_kelas' => 'RPL-A',
            'kuota_maksimal' => 30,
            'total_terisi' => 0,
        ]);
    }

    /**
     * Test 1: Graduated students are rejected from KRS course enrollment.
     */
    public function test_graduated_student_cannot_enroll_krs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Mahasiswa dengan status akademik 'Lulus Wisuda' tidak diizinkan melakukan pengisian KRS.");

        $krsService = app(SmartKrsService::class);
        $krsService->enrollClassWithPessimisticLock(
            $this->graduatedStudent->id,
            $this->kelasKuliah->id,
            $this->tahun->id
        );
    }

    /**
     * Test 2: Graduated students cannot submit DBPTA thesis proposal.
     */
    public function test_graduated_student_cannot_submit_dbpta_proposal(): void
    {
        $user = User::factory()->create([
            'id_siswa' => $this->graduatedStudent->id,
            'role' => 'siswa',
        ]);

        $response = $this->actingAs($user)->post(route('siakad.dbpta.proposal.store'), [
            'judul' => 'Sistem Rekomendasi Karir Alumni Berbasis AI',
            'abstrak' => 'Abstrak proposal skripsi mahasiswa yang sudah lulus.',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('tugas_akhirs', [
            'id_siswa' => $this->graduatedStudent->id,
            'judul' => 'Sistem Rekomendasi Karir Alumni Berbasis AI',
        ]);
    }

    /**
     * Test 3: Graduated students cannot register for KKN.
     */
    public function test_graduated_student_cannot_register_kkn(): void
    {
        $user = User::factory()->create([
            'id_siswa' => $this->graduatedStudent->id,
            'role' => 'siswa',
        ]);

        $response = $this->actingAs($user)->post(route('siakad.kkn.register'), [
            'lokasi_kkn' => 'Desa Sukamaju',
            'kelompok' => 'Kelompok 5',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('kkn_registrasis', [
            'id_siswa' => $this->graduatedStudent->id,
        ]);
    }

    /**
     * Test 4: Repeated courses calculation replaces lower grade and counts SKS only once.
     */
    public function test_passed_sks_calculation_replaces_repeated_course_grade(): void
    {
        $clearanceService = app(GraduationClearanceService::class);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF202',
            'nama_mk' => 'Struktur Data & Algoritma',
            'sks_total' => 4,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelas1 = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahun->id,
            'nama_kelas' => 'SDA-1',
            'kuota_maksimal' => 30,
            'total_terisi' => 1,
        ]);

        $krs1 = Krs::create([
            'id_siswa' => $this->activeStudent->id,
            'id_tahun_akademik' => $this->tahun->id,
            'max_sks_diizinkan' => 24,
            'total_sks_diambil' => 4,
            'status_krs' => 'Disetujui',
        ]);

        // Attempt 1: Grade D (bobot 1.0, not considered passing mandatory course)
        KrsDetail::create([
            'id_krs' => $krs1->id,
            'id_kelas_kuliah' => $kelas1->id,
            'nilai_akhir_huruf' => 'D',
            'bobot_mutu' => 1.0,
            'is_lulus' => false,
        ]);

        $sksBeforeRepeat = $clearanceService->getTotalPassedSks($this->activeStudent);
        $this->assertEquals(0, $sksBeforeRepeat);

        // Attempt 2 (Repeated): Grade A (bobot 4.0, passed)
        $kelas2 = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahun->id,
            'nama_kelas' => 'SDA-2',
            'kuota_maksimal' => 30,
            'total_terisi' => 1,
        ]);

        KrsDetail::create([
            'id_krs' => $krs1->id,
            'id_kelas_kuliah' => $kelas2->id,
            'nilai_akhir_huruf' => 'A',
            'bobot_mutu' => 4.0,
            'is_lulus' => true,
        ]);

        // Passed SKS must count the 4 SKS exactly ONCE, not 8 SKS
        $sksAfterRepeat = $clearanceService->getTotalPassedSks($this->activeStudent);
        $this->assertEquals(4, $sksAfterRepeat);
    }
}
