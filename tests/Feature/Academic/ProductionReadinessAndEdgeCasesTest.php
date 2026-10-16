<?php

namespace Tests\Feature\Academic;

use App\Models\BapPerkuliahan;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PresensiMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Services\Academic\SmartKrsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductionReadinessAndEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private TahunAkademik $tahunAkademik;
    private Kelas $kelas;
    private Spp $spp;
    private ProgramStudi $prodi;
    private Kurikulum $kurikulum;
    private \App\Models\Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = \App\Models\Guru::create([
            'nip' => '198501012010011001',
            'nama_guru' => 'Dr. Budi Santoso, M.T.',
            'jenis_kelamin' => 'L',
        ]);

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Informatika',
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'tgl_krs_mulai' => now()->subDays(5)->toDateString(),
            'tgl_krs_selesai' => now()->addDays(20)->toDateString(),
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI-5A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 4500000,
        ]);
    }

    /**
     * Test 1: Academic policy configurations loaded with authoritative defaults.
     */
    public function test_academic_configuration_defaults_loaded(): void
    {
        $this->assertEquals(7, config('academic.ukt_grace_period_days'));
        $this->assertEquals(180, config('academic.gps_attendance_retention_days'));
        $this->assertEquals(75, config('academic.min_attendance_exam_percent'));
        $this->assertEquals(100, config('academic.kkn_min_sks'));
        $this->assertEquals(110, config('academic.skripsi_min_sks'));
        $this->assertEquals(16, config('academic.bap_min_meetings_final'));
    }

    /**
     * Test 2: UKT grace period allows KRS registration when invoice is within due date + grace days.
     */
    public function test_ukt_grace_period_allows_krs_within_due_window(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0077889911',
            'nis' => '778891',
            'nama' => 'Student Grace Period Active',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '0812345001',
            'status_kelulusan' => 'Aktif',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => '778891',
            'nisn' => '0077889911',
            'nama' => 'Student Grace Period Active',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Aktif',
        ]);

        // Invoice is not yet paid, but due date is tomorrow (within grace period)
        TagihanUkt::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '9880077889911',
            'nomor_invoice' => 'INV-2026-GRACE-01',
            'total_tagihan' => 4500000,
            'total_harus_bayar' => 4500000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDay(),
        ]);

        $krsService = app(SmartKrsService::class);
        $isCleared = $krsService->isFinanciallyCleared($mahasiswa->id, $this->tahunAkademik->id);

        $this->assertTrue($isCleared, 'Student within UKT grace period must be financially permitted to register KRS.');
    }

    /**
     * Test 3: UKT overdue invoice beyond grace window blocks KRS registration.
     */
    public function test_ukt_grace_period_locks_krs_after_grace_window_expires(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0077889922',
            'nis' => '778892',
            'nama' => 'Student Overdue Blocked',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '0812345002',
            'status_kelulusan' => 'Aktif',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => '778892',
            'nisn' => '0077889922',
            'nama' => 'Student Overdue Blocked',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Aktif',
        ]);

        // Invoice was due 20 days ago (grace period is 7 days, so this is 13 days past grace expiration)
        TagihanUkt::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '9880077889922',
            'nomor_invoice' => 'INV-2026-OVERDUE-02',
            'total_tagihan' => 4500000,
            'total_harus_bayar' => 4500000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->subDays(20),
        ]);

        $krsService = app(SmartKrsService::class);
        $isCleared = $krsService->isFinanciallyCleared($mahasiswa->id, $this->tahunAkademik->id);

        $this->assertFalse($isCleared, 'Student with expired UKT invoice beyond grace period must be locked from KRS registration.');
    }

    /**
     * Test 4: Attendance telemetry dry-run previews matching records without mutating database.
     */
    public function test_attendance_telemetry_dry_run_does_not_mutate_coordinates(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nim' => '778893',
            'nisn' => '0077889933',
            'nama' => 'Student Telemetry 1',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF501',
            'nama_mk' => 'Keamanan Sistem',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-5A-SEC',
            'kuota' => 40,
        ]);

        $bap = BapPerkuliahan::create([
            'id_guru' => $this->guru->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'pertemuan_ke' => 1,
            'tanggal_pelaksanaan' => now()->subDays(200),
            'jam_mulai_real' => '08:00:00',
            'jam_selesai_real' => '10:30:00',
            'materi_pembahasan' => 'Pengantar Keamanan Jaringan',
            'status_verifikasi' => 'Tersubmit',
        ]);

        $presensi = PresensiMahasiswa::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_bap' => $bap->id,
            'waktu_hadir' => now()->subDays(200),
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'device_fingerprint' => 'BROWSER_FP_ABC123456',
            'status' => 'Hadir',
        ]);

        $exitCode = Artisan::call('attendance:archive-telemetry', [
            '--days' => 180,
            '--dry-run' => true,
        ]);

        $this->assertEquals(0, $exitCode);

        // Assert record still holds coordinates after dry-run
        $freshPresensi = $presensi->fresh();
        $this->assertNotNull($freshPresensi->latitude);
        $this->assertNotNull($freshPresensi->longitude);
        $this->assertNotNull($freshPresensi->device_fingerprint);
    }

    /**
     * Test 5: Attendance telemetry archive scrubs coordinates while preserving academic status.
     */
    public function test_attendance_telemetry_scrubs_old_coordinates_while_preserving_status(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nim' => '778894',
            'nisn' => '0077889944',
            'nama' => 'Student Telemetry 2',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Aktif',
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF502',
            'nama_mk' => 'Arsitektur Komputasi Awan',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-5A-CLOUD',
            'kuota' => 40,
        ]);

        $bap = BapPerkuliahan::create([
            'id_guru' => $this->guru->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'pertemuan_ke' => 2,
            'tanggal_pelaksanaan' => now()->subDays(210),
            'jam_mulai_real' => '13:00:00',
            'jam_selesai_real' => '15:30:00',
            'materi_pembahasan' => 'Cloud Native Architectures',
            'status_verifikasi' => 'Tersubmit',
        ]);

        $presensi = PresensiMahasiswa::create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_bap' => $bap->id,
            'waktu_hadir' => now()->subDays(210),
            'latitude' => -6.914744,
            'longitude' => 107.609810,
            'device_fingerprint' => 'MOBILE_FP_XYZ987654',
            'status' => 'Hadir',
        ]);

        // Execute live telemetry archival command
        $exitCode = Artisan::call('attendance:archive-telemetry', [
            '--days' => 180,
        ]);

        $this->assertEquals(0, $exitCode);

        // Coordinates are minimized to 0.0 and fingerprint is nullified (UU PDP compliance)
        $freshPresensi = $presensi->fresh();
        $this->assertEquals(0.0, (float) $freshPresensi->latitude);
        $this->assertEquals(0.0, (float) $freshPresensi->longitude);
        $this->assertNull($freshPresensi->device_fingerprint);

        // Academic attendance status must remain intact!
        $this->assertEquals('Hadir', $freshPresensi->status);
        $this->assertNotNull($freshPresensi->waktu_hadir);
    }

    /**
     * Test 6: MySQL database configuration enforces strict mode and prefix indexes.
     */
    public function test_mysql_database_configuration_strictness_and_indexes(): void
    {
        $mysqlConfig = config('database.connections.mysql');

        $this->assertEquals('mysql', $mysqlConfig['driver']);
        $this->assertTrue($mysqlConfig['strict']);
        $this->assertEquals('utf8mb4', $mysqlConfig['charset']);
        $this->assertEquals('utf8mb4_unicode_ci', $mysqlConfig['collation']);
        $this->assertTrue($mysqlConfig['prefix_indexes']);
    }
}
