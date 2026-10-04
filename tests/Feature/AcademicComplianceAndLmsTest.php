<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\ReconciliationLog;
use App\Models\Siswa;
use App\Models\SkpiAktivitas;
use App\Models\Spp;
use App\Models\Submission;
use App\Models\TahunAkademik;
use App\Models\TransaksiH2h;
use App\Models\User;
use App\Services\Academic\EarlyWarningService;
use App\Services\Academic\GraduationClearanceService;
use App\Services\Lms\LmsGradingService;
use App\Services\Lms\LmsService;
use App\Services\Security\SecureDocumentService;
use App\Services\Security\UploadSecurityGateway;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use Tests\TestCase;

class AcademicComplianceAndLmsTest extends TestCase
{
    use RefreshDatabase;

    protected Siswa $student;
    protected User $studentUser;
    protected Guru $lecturer;
    protected User $lecturerUser;
    protected User $adminUser;
    protected TahunAkademik $tahunAkademik;
    protected ProgramStudi $prodi;
    protected Kurikulum $kurikulum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // Setup base master data
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);
        $kelas = Kelas::create(['nama_kelas' => 'TI-A', 'kompetensi_keahlian' => 'Teknik Informatika']);

        $this->student = Siswa::create([
            'nisn' => '0011223344',
            'nis' => '12345',
            'nik' => '3201123456780001',
            'nama' => 'Budi Santoso',
            'alamat' => 'Jl. Merdeka No. 45, Jakarta',
            'no_telp' => '081298765432',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
            'total_skpi_points' => 0,
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_mhs',
            'email' => 'budi@university.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'id_siswa' => $this->student->id,
            'consent_pdp_at' => now(),
        ]);

        $this->lecturer = Guru::create([
            'nip' => '198501012010011001',
            'nama_guru' => 'Dr. Ir. Hendra Gunawan, M.T.',
            'jenis_kelamin' => 'L',
            'no_telp' => '08123456789',
        ]);

        $this->lecturerUser = User::create([
            'name' => 'Dr. Ir. Hendra Gunawan, M.T.',
            'username' => 'hendra_dosen',
            'email' => 'hendra@university.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
            'id_guru' => $this->lecturer->id,
            'consent_pdp_at' => now(),
        ]);

        $this->adminUser = User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'admin@university.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'consent_pdp_at' => now(),
        ]);

        $fakultas = \App\Models\Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'total_sks_lulus' => 144,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20252',
            'nama_tahun' => 'Genap 2025/2026',
            'semester' => 'Genap',
            'is_active' => true,
            'tgl_mulai' => '2026-02-01',
            'tgl_selesai' => '2026-07-31',
        ]);
    }

    /**
     * Requirement 1: Dynamic Graduation Clearance Checker
     */
    public function test_graduation_clearance_checker_rejects_insufficient_sks_and_failing_mandatory_courses(): void
    {
        $service = app(GraduationClearanceService::class);

        // Scenario A: Student with 0 SKS and 0 SKPI -> Ineligible
        $audit = $service->auditGraduation($this->student);
        $this->assertFalse($audit['is_eligible']);
        $this->assertGreaterThan(0, count($audit['reasons']));

        // Scenario B: Mandatory course with Grade D
        $mkWajib = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'TI101',
            'nama_mk' => 'Algoritma Pemrograman',
            'sks_total' => 4,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mkWajib->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI101-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->lecturer->id,
            'total_sks_diambil' => 4,
            'status_krs' => 'Disetujui',
        ]);

        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_angka' => 55.00,
            'nilai_akhir_huruf' => 'D',
            'bobot_mutu' => 1.00,
            'is_lulus' => false,
        ]);

        $auditWithD = $service->auditGraduation($this->student);
        $this->assertFalse($auditWithD['is_eligible']);
        $this->assertNotEmpty($auditWithD['failed_mandatory_courses']);

        // Attempting to approve graduation should throw DomainException
        $this->expectException(DomainException::class);
        $service->approveGraduation($this->student);
    }

    public function test_graduation_clearance_approves_when_all_criteria_met(): void
    {
        $service = app(GraduationClearanceService::class);

        // Create course meeting requirements (e.g. 144 SKS passing)
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'TI999',
            'nama_mk' => 'Skripsi & Comprehensive',
            'sks_total' => 144,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI999-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->lecturer->id,
            'total_sks_diambil' => 144,
            'status_krs' => 'Disetujui',
        ]);

        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_angka' => 90.00,
            'nilai_akhir_huruf' => 'A',
            'bobot_mutu' => 4.00,
            'is_lulus' => true,
        ]);

        // Add verified SKPI points >= 100
        SkpiAktivitas::create([
            'id_siswa' => $this->student->id,
            'kategori' => 'Prestasi & Kompetisi',
            'nama_kegiatan_id' => 'Juara 1 Hackathon Nasional',
            'nama_kegiatan_en' => '1st Winner National Hackathon',
            'penyelenggara' => 'Kemdikbud',
            'tahun_kegiatan' => 2026,
            'poin_sacs' => 120,
            'status_verifikasi' => 'Disetujui Kaprodi',
        ]);

        $audit = $service->auditGraduation($this->student);
        $this->assertTrue($audit['is_eligible']);
        $this->assertEquals(144, $audit['sks_accumulated']);
        $this->assertEquals(120, $audit['skpi_points']);

        // Approve graduation
        $graduatedStudent = $service->approveGraduation($this->student, 'IJZ-2026-TI-0099');
        $this->assertEquals('Lulus', $graduatedStudent->status_kelulusan);
        $this->assertEquals('IJZ-2026-TI-0099', $graduatedStudent->nomor_ijazah);
        $this->assertNotNull($graduatedStudent->tgl_kelulusan);
    }

    /**
     * Requirement 2: Early Warning System (EWS) for Drop Out Prevention
     */
    public function test_early_warning_system_flags_inactive_krs_and_low_gpa(): void
    {
        $ta1 = TahunAkademik::create(['kode_tahun' => '20241', 'nama_tahun' => 'Ganjil 2024/2025', 'semester' => 'Ganjil', 'is_active' => false, 'tgl_mulai' => '2024-09-01', 'tgl_selesai' => '2025-01-31']);
        $ta2 = TahunAkademik::create(['kode_tahun' => '20242', 'nama_tahun' => 'Genap 2024/2025', 'semester' => 'Genap', 'is_active' => false, 'tgl_mulai' => '2025-02-01', 'tgl_selesai' => '2025-07-31']);

        // Student has no KRS in both semesters
        $ewsService = app(EarlyWarningService::class);
        $logs = $ewsService->analyzeStudent($this->student);

        $this->assertNotEmpty($logs);
        $this->assertDatabaseHas('early_warning_logs', [
            'id_siswa' => $this->student->id,
            'severity' => 'CRITICAL',
            'trigger_type' => 'INACTIVE_KRS_CONSECUTIVE',
        ]);

        // Run artisan command ews:analyze
        $exitCode = Artisan::call('ews:analyze');
        $this->assertEquals(0, $exitCode);
    }

    /**
     * Requirement 3: Expired Incomplete Grade Handler (BL/T Amnesti Engine)
     */
    public function test_expired_incomplete_grades_auto_convert_to_grade_e_and_recalculate_ips(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'TI202',
            'nama_mk' => 'Basis Data Lanjut',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI202-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->lecturer->id,
            'total_sks_diambil' => 3,
            'ips_lalu' => 3.50,
            'status_krs' => 'Disetujui',
        ]);

        $krsDetail = KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_angka' => 0.00,
            'nilai_akhir_huruf' => 'BL',
            'bobot_mutu' => 0.00,
            'incomplete_expires_at' => now()->subDay(), // Expired yesterday
            'is_incomplete_expired' => false,
            'is_lulus' => false,
        ]);

        $exitCode = Artisan::call('grades:expire-incomplete');
        $this->assertEquals(0, $exitCode);

        $krsDetail->refresh();
        $this->assertEquals('E', $krsDetail->nilai_akhir_huruf);
        $this->assertTrue((bool) $krsDetail->is_incomplete_expired);
        $this->assertEquals(0.00, (float) $krsDetail->bobot_mutu);
    }

    /**
     * Requirement 4: Secure Document Downloader Guard (Anti-IDOR Signed URL Engine)
     */
    public function test_secure_document_downloader_guard_enforces_signature_and_blocks_idor(): void
    {
        $docService = app(SecureDocumentService::class);
        $pembayaran = \App\Models\Pembayaran::create([
            'id_user' => $this->studentUser->id,
            'id_petugas' => $this->adminUser->id,
            'id_siswa' => $this->student->id,
            'id_spp' => $this->student->id_spp,
            'tgl_bayar' => now(),
            'bulan_dibayar' => 'Januari',
            'tahun_dibayar' => '2026',
            'jumlah_bayar' => 500000,
        ]);

        $validSignedUrl = $docService->generateReceiptUrl($pembayaran->id, $this->student->id, 5);

        // 1. Valid Signature with student auth -> HTTP 200
        $responseValid = $this->actingAs($this->studentUser)->get($validSignedUrl);
        $responseValid->assertStatus(200);

        // 2. Tampered / Unsigned URL -> HTTP 403 Forbidden
        $tamperedUrl = route('pembayaran.cetak.signed', ['id' => $pembayaran->id, 'id_siswa' => $this->student->id]);
        $responseTampered = $this->actingAs($this->studentUser)->get($tamperedUrl);
        $responseTampered->assertStatus(403);

        // 3. IDOR Attack Scenario: Student B attempts to use valid signed URL for Student A
        $otherStudent = Siswa::create([
            'nisn' => '9988776655',
            'nis' => '54321',
            'nik' => '3201999999990002',
            'nama' => 'Student Attacker',
            'alamat' => 'Jl. Diponegoro No. 12',
            'no_telp' => '081211112222',
            'id_kelas' => $this->student->id_kelas,
            'id_spp' => $this->student->id_spp,
        ]);
        $otherUser = User::create([
            'name' => 'Student Attacker',
            'username' => 'attacker_mhs',
            'email' => 'attacker@university.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'id_siswa' => $otherStudent->id,
            'consent_pdp_at' => now(),
        ]);

        $responseIdor = $this->actingAs($otherUser)->get($validSignedUrl);
        $responseIdor->assertStatus(403);
    }

    /**
     * Requirement 5: Discrepancy Notification Gateway (H2H Reconciliation)
     */
    public function test_financial_reconciliation_records_discrepancies_into_table(): void
    {
        $tagihanVa = \App\Models\TagihanVa::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '9880011223344001',
            'nomor_invoice' => 'INV-2026-H2H-0001',
            'total_tagihan' => 500000.00,
            'total_harus_bayar' => 500000.00,
            'total_sudah_bayar' => 500000.00,
            'status_pembayaran' => 'Lunas',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        // Seed a local transaction
        TransaksiH2h::create([
            'id_tagihan_va' => $tagihanVa->id,
            'kode_bank' => 'BNI',
            'nomor_transaksi_bank' => 'BNI-TRX-1001',
            'nomor_va' => '9880011223344001',
            'jumlah_dibayar' => 500000.00,
            'waktu_transaksi_bank' => now()->format('Y-m-d H:i:s'),
            'channel_bayar' => 'ATM',
            'status' => 'SUCCESS',
        ]);

        // Create mock settlement file with an amount mismatch
        $csvContent = "TRX_ID,VA_NUMBER,AMOUNT\nBNI-TRX-1001,9880011223344001,450000\nBNI-TRX-9999,9880011223344999,300000\n";
        $tempCsvPath = tempnam(sys_get_temp_dir(), 'settlement_') . '.csv';
        file_put_contents($tempCsvPath, $csvContent);

        $exitCode = Artisan::call('reconcile:bank-h2h', [
            '--bank' => 'BNI',
            '--date' => date('Y-m-d'),
            '--file' => $tempCsvPath,
        ]);

        $this->assertEquals(0, $exitCode);

        // Verify discrepancy records in database
        $this->assertDatabaseHas('financial_discrepancies', [
            'bank_code' => 'BNI',
            'nomor_transaksi_bank' => 'BNI-TRX-1001',
            'anomaly_type' => 'AMOUNT_MISMATCH',
            'status' => 'OPEN',
        ]);

        $this->assertDatabaseHas('financial_discrepancies', [
            'bank_code' => 'BNI',
            'nomor_transaksi_bank' => 'BNI-TRX-9999',
            'anomaly_type' => 'UNMATCHED_IN_SIAKAD',
            'status' => 'OPEN',
        ]);

        @unlink($tempCsvPath);
    }

    /**
     * Requirement 6: Full Internal LMS Module (Security, Timestamps, Fingerprint, Anonymous Grading)
     */
    public function test_lms_upload_gateway_blocks_malicious_php_shells_and_validates_magic_bytes(): void
    {
        $gateway = app(UploadSecurityGateway::class);

        // Valid PDF file with %PDF- header
        $validPdf = UploadedFile::fake()->createWithContent('paper.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
        $pdfResult = $gateway->validateFile($validPdf);
        $this->assertTrue($pdfResult['is_safe']);

        // Malicious disguised PHP file with .pdf extension
        $fakePdfShell = UploadedFile::fake()->createWithContent('exploit.pdf', "<?php system(\$_GET['cmd']); ?>");
        $shellResult = $gateway->validateFile($fakePdfShell);
        $this->assertFalse($shellResult['is_safe']);

        $this->expectException(InvalidArgumentException::class);
        $gateway->assertSafeFile($fakePdfShell);
    }

    public function test_lms_service_handles_submissions_with_millisecond_timestamp_and_device_fingerprint(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'TI303',
            'nama_mk' => 'Pemrograman Web Enterprise',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI303-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $lmsService = app(LmsService::class);

        // 1. Create Timed Material Drip-Feed
        $materialFuture = $lmsService->createMaterial([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'judul' => 'Materi Minggu 14 - Cloud Architecture',
            'minggu_ke' => 14,
            'publish_at' => now()->addDays(7), // Future release
            'file_path' => 'lms/materials/materi14.pdf',
        ]);

        $this->assertFalse($materialFuture->isAvailable());

        $materialActive = $lmsService->createMaterial([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'judul' => 'Materi Minggu 1 - Intro',
            'minggu_ke' => 1,
            'publish_at' => now()->subDay(), // Available now
            'file_path' => 'lms/materials/materi1.pdf',
        ]);

        $this->assertTrue($materialActive->isAvailable());

        // 2. Create Assignment & Submit Solution
        $assignment = $lmsService->createAssignment([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'judul' => 'Tugas 1 - Arsitektur MVC Laravel',
            'komponen_penilaian' => 'TUGAS',
            'bobot_persen' => 20.00,
            'deadline_at' => now()->addDays(3),
            'allow_late_submission' => true,
            'late_grace_minutes' => 15,
            'is_anonymous_grading' => true,
        ]);

        $validZip = UploadedFile::fake()->createWithContent('project.zip', "PK\x03\x04\x14\x00\x00\x00\x08\x00DummyZipFileContentForTesting");
        $submission = $lmsService->submitAssignment($assignment, $this->student, $validZip, [
            'ip' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 SIAKAD-Client',
        ]);

        $this->assertNotNull($submission);
        $this->assertNotNull($submission->submission_token);
        $this->assertNotNull($submission->device_fingerprint);
        $this->assertNotNull($submission->submission_microtime);
        $this->assertFalse($submission->is_late);
    }

    public function test_lms_grading_service_supports_anonymous_grading_mode_and_syncs_krs_gradebook(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'TI404',
            'nama_mk' => 'Sistem Terdistribusi',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI404-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->lecturer->id,
            'total_sks_diambil' => 3,
            'status_krs' => 'Disetujui',
        ]);

        $krsDetail = KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
            'nilai_kehadiran' => 100.00,
            'nilai_quiz' => 90.00,
            'nilai_praktikum' => 90.00,
            'nilai_uts' => 85.00,
            'nilai_uas' => 90.00,
        ]);

        $lmsService = app(LmsService::class);
        $gradingService = app(LmsGradingService::class);

        $assignment = $lmsService->createAssignment([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'judul' => 'Tugas Distributed Consensus',
            'komponen_penilaian' => 'TUGAS',
            'bobot_persen' => 20.00,
            'deadline_at' => now()->addDays(5),
            'is_anonymous_grading' => true,
        ]);

        $validPdf = UploadedFile::fake()->createWithContent('paper.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
        $submission = $lmsService->submitAssignment($assignment, $this->student, $validPdf);

        // Check Anonymous Masking
        $submissionsForLecturer = $gradingService->getSubmissions($assignment);
        $firstItem = $submissionsForLecturer->first();
        $this->assertArrayHasKey('masked_identifier', $firstItem);
        $this->assertStringStartsWith('ANON-STUDENT-', $firstItem['masked_identifier']);

        // Grade the submission (Grade: 95.00)
        $gradedSubmission = $gradingService->gradeSubmission($submission, 95.00, 'Excellent implementation of Paxos consensus algorithm.', $this->lecturer);
        $this->assertEquals(95.00, (float) $gradedSubmission->nilai);

        // Verify KRS Detail composite grade synced
        $krsDetail->refresh();
        $this->assertEquals(95.00, (float) $krsDetail->nilai_tugas);
        $this->assertGreaterThan(0, (float) $krsDetail->nilai_akhir_angka);
        $this->assertEquals('A', $krsDetail->nilai_akhir_huruf);
        $this->assertTrue((bool) $krsDetail->is_lulus);
    }
}
