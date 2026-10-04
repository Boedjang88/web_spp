<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PembayaranUkt;
use App\Models\ProgramStudi;
use App\Models\Ruangan;
use App\Models\SkpiAktivitas;
use App\Models\Submission;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\Ukt;
use App\Models\User;
use App\Services\Academic\EarlyWarningService;
use App\Services\Academic\GraduationClearanceService;
use App\Services\Finance\UktBillingService;
use App\Services\Lms\LmsGradingService;
use App\Services\Lms\LmsService;
use App\Services\Security\UploadSecurityGateway;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Tests\TestCase;

class UniversityComplianceAndLmsTest extends TestCase
{
    use RefreshDatabase;

    protected Mahasiswa $mahasiswa;
    protected User $mahasiswaUser;
    protected Dosen $dosen;
    protected User $dosenUser;
    protected User $baakUser;
    protected User $superAdminUser;
    protected ProgramStudi $prodi;
    protected Kurikulum $kurikulum;
    protected TahunAkademik $tahunAkademik;
    protected Ukt $ukt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // 1. Setup Master Academic Structure
        $fakultas = Fakultas::create([
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

        $this->ukt = Ukt::create([
            'id_prodi' => $this->prodi->id,
            'tahun' => 2026,
            'kelompok_ukt' => 'UKT 4',
            'nominal' => 4500000.00,
            'biaya_praktikum' => 500000.00,
            'biaya_kemahasiswaan' => 50000.00,
            'deskripsi' => 'UKT Reguler S1 Teknik Informatika',
        ]);

        // 2. Setup Dosen (University Lecturer)
        $this->dosen = Dosen::create([
            'nidn' => '0412058501',
            'nidk' => 'DK-0412058501',
            'nip' => '198505122010121001',
            'nama_dosen' => 'Prof. Dr. Ir. Rahmat Hidayat, M.Kom.',
            'gelar_depan' => 'Prof. Dr.',
            'gelar_belakang' => 'M.Kom.',
            'jenis_kelamin' => 'L',
            'no_telp' => '081234567890',
            'email' => 'rahmat.hidayat@univ.ac.id',
            'id_prodi' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->dosenUser = User::create([
            'name' => 'Prof. Dr. Ir. Rahmat Hidayat, M.Kom.',
            'email' => 'rahmat.dosen@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'dosen',
            'id_dosen' => $this->dosen->id,
            'consent_pdp_at' => now(),
        ]);

        // 3. Setup Mahasiswa (University Student)
        $this->mahasiswa = Mahasiswa::create([
            'nim' => '2301010045',
            'nisn' => '0054321987',
            'nik' => '3201123456780009',
            'nama' => 'Aditya Pratama',
            'id_prodi' => $this->prodi->id,
            'id_dosen_pa' => $this->dosen->id,
            'id_ukt' => $this->ukt->id,
            'alamat' => 'Jl. Boulevard Kampus No. 10, Bandung',
            'nama_ibu_kandung' => 'Siti Aminah',
            'no_telp' => '081398765432',
            'status_kelulusan' => 'Aktif',
            'total_skpi_points' => 0,
            'consent_pdp_at' => now(),
        ]);

        $spp = \App\Models\Spp::create(['tahun' => 2026, 'nominal' => 4500000]);
        $kelas = \App\Models\Kelas::create(['nama_kelas' => 'TI-1A', 'kompetensi_keahlian' => 'Teknik Informatika']);

        $guru = \App\Models\Guru::create([
            'nip' => '198505122010121001',
            'nama_guru' => 'Prof. Dr. Ir. Rahmat Hidayat, M.Kom.',
            'jenis_kelamin' => 'L',
            'no_telp' => '081234567890',
        ]);

        $siswa = \App\Models\Siswa::create([
            'nisn' => '0054321987',
            'nis' => '2301010045',
            'nik' => '3201123456780009',
            'nama' => 'Aditya Pratama',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Boulevard Kampus No. 10, Bandung',
            'no_telp' => '081398765432',
            'status_kelulusan' => 'Aktif',
            'total_skpi_points' => 0,
            'consent_pdp_at' => now(),
        ]);

        $this->mahasiswaUser = User::create([
            'name' => 'Aditya Pratama',
            'email' => 'aditya.mhs@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'mahasiswa',
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_siswa' => $siswa->id,
            'consent_pdp_at' => now(),
        ]);

        // 4. Setup BAAK & Superadmin Users
        $this->baakUser = User::create([
            'name' => 'Admin BAAK Utama',
            'email' => 'baak@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'baak',
            'consent_pdp_at' => now(),
        ]);

        $this->superAdminUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'superadmin',
            'consent_pdp_at' => now(),
        ]);
    }

    /**
     * Test 1: University Entity Refactoring & 4-Tier RBAC Hierarchy IDOR Protection
     */
    public function test_4_tier_rbac_hierarchy_and_idor_protection(): void
    {
        $this->assertTrue($this->superAdminUser->isSuperAdmin());
        $this->assertTrue($this->baakUser->isBaak());
        $this->assertTrue($this->dosenUser->isDosen());
        $this->assertTrue($this->mahasiswaUser->isMahasiswa());

        // Create second student for IDOR check
        $otherMahasiswa = Mahasiswa::create([
            'nim' => '2301010099',
            'nama' => 'Student Other',
            'id_prodi' => $this->prodi->id,
            'alamat' => 'Jl. Merdeka No. 99',
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);
        $otherUser = User::create([
            'name' => 'Student Other',
            'email' => 'other.mhs@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'mahasiswa',
            'id_mahasiswa' => $otherMahasiswa->id,
            'consent_pdp_at' => now(),
        ]);

        $policy = new \App\Policies\MahasiswaPolicy();

        // Mahasiswa A can view own profile
        $this->assertTrue($policy->view($this->mahasiswaUser, $this->mahasiswa));

        // Mahasiswa A CANNOT view or edit Mahasiswa B (IDOR Blocked)
        $this->assertFalse($policy->view($this->mahasiswaUser, $otherMahasiswa));
        $this->assertFalse($policy->update($this->mahasiswaUser, $otherMahasiswa));

        // BAAK and Superadmin can view any student
        $this->assertTrue($policy->view($this->baakUser, $otherMahasiswa));
        $this->assertTrue($policy->view($this->superAdminUser, $otherMahasiswa));
    }

    /**
     * Test 2: UKT Invoicing & Bank H2H Webhook Idempotency (24h Cache State Lock)
     */
    public function test_ukt_invoicing_and_h2h_webhook_idempotency(): void
    {
        $billingService = app(UktBillingService::class);
        $tagihan = $billingService->generateSemesterBill($this->mahasiswa, $this->tahunAkademik);

        $this->assertNotNull($tagihan);
        $this->assertEquals(5050000.00, (float) $tagihan->total_harus_bayar); // 4.5M + 500k + 50k
        $this->assertEquals('Belum Bayar', $tagihan->status_pembayaran);

        // Simulate H2H Bank Callback with IdempotencyMiddleware
        $payload = [
            'nomor_va' => $tagihan->nomor_va,
            'nomor_transaksi_bank' => 'BNI-TRX-' . uniqid(),
            'jumlah_bayar' => 5050000.00,
            'kode_bank' => 'BNI',
            'channel_bayar' => 'ATM',
        ];

        // 1st request -> processes successfully
        $response1 = $this->withHeaders([
            'Idempotency-Key' => $payload['nomor_transaksi_bank'],
        ])->postJson('/api/v1/h2h/callback', $payload);

        $response1->assertStatus(200);
        $response1->assertJsonPath('data.status', 'SUCCESS');

        // Verify Tagihan status is Lunas
        $tagihan->refresh();
        $this->assertEquals('Lunas', $tagihan->status_pembayaran);

        // 2nd identical concurrent/replay request -> returns idempotent cached response immediately
        $response2 = $this->withHeaders([
            'Idempotency-Key' => $payload['nomor_transaksi_bank'],
        ])->postJson('/api/v1/h2h/callback', $payload);

        $response2->assertStatus(200);
        $response2->assertHeader('X-Idempotent-Replay', 'true');
    }

    /**
     * Test 3: Geo-Fenced Student Attendance with Haversine Formula (<= 20m) and 10s Expiring QrToken
     */
    public function test_geo_attendance_enforces_20m_radius_and_10s_qr_token(): void
    {
        $gedung = \App\Models\Gedung::create([
            'kode_gedung' => 'LAB',
            'nama_gedung' => 'Gedung Laboratorium Komputer',
        ]);

        $ruangan = Ruangan::create([
            'id_gedung' => $gedung->id,
            'kode_ruangan' => 'LAB-301',
            'nama_ruangan' => 'Lab Software Engineering',
            'kapasitas' => 40,
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'radius_meter' => 20,
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF201',
            'nama_mk' => 'Struktur Data & Algoritma',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF201-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $bap = \App\Models\BapPerkuliahan::create([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_guru' => $this->dosen->id,
            'id_ruangan' => $ruangan->id,
            'pertemuan_ke' => 1,
            'tanggal_pelaksanaan' => now()->format('Y-m-d'),
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:30',
            'materi_pembahasan' => 'Graph Theory & Shortest Path',
            'status_verifikasi' => 'Diverifikasi BAAK',
        ]);

        // 1. Generate 10-Second QR Token
        $tokenResponse = $this->actingAs($this->dosenUser, 'sanctum')
            ->postJson("/api/v1/attendance/bap/{$bap->id}/token");

        $tokenResponse->assertStatus(200);
        $tokenData = $tokenResponse->json('data');
        $qrToken = $tokenData['qr_token'];
        $this->assertEquals(10, $tokenData['expires_in_seconds']);

        // 2. Submit Valid Geo-Coordinate (Right inside the room: ~2 meters)
        $validAttendance = $this->actingAs($this->mahasiswaUser, 'sanctum')
            ->postJson('/api/v1/attendance/submit', [
                'id_bap' => $bap->id,
                'qr_token' => $qrToken,
                'latitude' => -6.917466,
                'longitude' => 107.619125,
            ]);

        $validAttendance->assertStatus(200);
        $validAttendance->assertJsonPath('data.status', 'HADIR_VERIFIED');

        // 3. Submit Invalid Geo-Coordinate (> 20 meters away, e.g. 100m)
        $farAttendance = $this->actingAs($this->mahasiswaUser, 'sanctum')
            ->postJson('/api/v1/attendance/submit', [
                'id_bap' => $bap->id,
                'qr_token' => $qrToken,
                'latitude' => -6.919000,
                'longitude' => 107.625000,
            ]);

        $farAttendance->assertStatus(422);

        // 4. Submit Expired Token (simulate expired token in Cache)
        Cache::forget("qr_attendance_bap_{$bap->id}");
        $expiredAttendance = $this->actingAs($this->mahasiswaUser, 'sanctum')
            ->postJson('/api/v1/attendance/submit', [
                'id_bap' => $bap->id,
                'qr_token' => $qrToken,
                'latitude' => -6.917464,
                'longitude' => 107.619123,
            ]);

        $expiredAttendance->assertStatus(422);
    }

    /**
     * Test 4: LMS Upload Security Gateway (Magic Numbers Verification & Web Shell Blocking)
     */
    public function test_lms_upload_gateway_blocks_php_shells_and_accepts_valid_documents(): void
    {
        $gateway = app(UploadSecurityGateway::class);

        // Valid PDF file with %PDF- header
        $validPdf = UploadedFile::fake()->createWithContent('laporan.pdf', "%PDF-1.7\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
        $resultPdf = $gateway->validateFile($validPdf);
        $this->assertTrue($resultPdf['is_safe']);

        // Valid DOCX/ZIP file with PK\x03\x04 header
        $validDocx = UploadedFile::fake()->createWithContent('skripsi.docx', "PK\x03\x04\x14\x00\x00\x00\x08\x00DummyDocxBinaryContent");
        $resultDocx = $gateway->validateFile($validDocx);
        $this->assertTrue($resultDocx['is_safe']);

        // Disguised PHP shell payload (.pdf extension with PHP executable code)
        $maliciousShell = UploadedFile::fake()->createWithContent('malware.pdf', "<?php system(\$_GET['cmd']); ?>");
        $resultShell = $gateway->validateFile($maliciousShell);
        $this->assertFalse($resultShell['is_safe']);

        $this->expectException(InvalidArgumentException::class);
        $gateway->assertSafeFile($maliciousShell);
    }

    /**
     * Test 5: LMS Anonymous Grading Mode and Multi-Class Assignment Distribution
     */
    public function test_lms_anonymous_grading_and_multi_class_distribution(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF301',
            'nama_mk' => 'Pemrograman Sistem & Cloud',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasA = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF301-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $kelasB = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF301-B',
            'kuota_maksimal' => 40,
            'total_terisi' => 0,
        ]);

        $lmsService = app(LmsService::class);
        $gradingService = app(LmsGradingService::class);

        // Push task across multiple parallel classes (Kelas A & Kelas B)
        $assignment = $lmsService->createAssignment([
            'id_kelas_kuliah' => $kelasA->id,
            'target_kelas_ids' => [$kelasA->id, $kelasB->id],
            'judul' => 'Proyek Kubernetes Cluster & High Availability',
            'komponen_penilaian' => 'TUGAS',
            'bobot_persen' => 25.00,
            'deadline_at' => now()->addDays(7),
            'is_anonymous_grading' => true,
        ]);

        $this->assertContains($kelasB->id, $assignment->target_kelas_ids);

        // Student submits assignment
        $validSubmissionFile = UploadedFile::fake()->createWithContent('submission.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
        $submission = $lmsService->submitAssignment($assignment, $this->mahasiswa, $validSubmissionFile, [
            'ip' => '10.0.0.1',
            'user_agent' => 'Mozilla/5.0 SIAKAD-Client-Browser',
        ]);

        $this->assertNotNull($submission->submission_token);
        $this->assertNotNull($submission->device_fingerprint);

        // Lecturer retrieves submissions in Anonymous Grading Mode
        $submissionsList = $gradingService->getSubmissions($assignment);
        $firstItem = $submissionsList->first();

        $this->assertArrayHasKey('masked_identifier', $firstItem);
        $this->assertStringStartsWith('ANON-STUDENT-', $firstItem['masked_identifier']);
        $this->assertArrayNotHasKey('nama', $firstItem); // Student name is masked
    }

    /**
     * Test 6: Expired Incomplete Grade Auto-Expiration (30-day BL/T -> E)
     */
    public function test_expired_incomplete_grades_auto_expiration(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF401',
            'nama_mk' => 'Keamanan Siber & Kriptografi',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF401-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->dosen->id,
            'total_sks_diambil' => 3,
            'ips_lalu' => 3.80,
            'status_krs' => 'Disetujui',
        ]);

        $krsDetail = KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_angka' => 0.00,
            'nilai_akhir_huruf' => 'BL',
            'bobot_mutu' => 0.00,
            'incomplete_expires_at' => now()->subDay(), // 30 days expired
            'is_incomplete_expired' => false,
            'is_lulus' => false,
        ]);

        Artisan::call('grades:expire-incomplete');

        $krsDetail->refresh();
        $this->assertEquals('E', $krsDetail->nilai_akhir_huruf);
        $this->assertTrue((bool) $krsDetail->is_incomplete_expired);
        $this->assertEquals(0.00, (float) $krsDetail->bobot_mutu);
    }

    /**
     * Test 7: Extended Academic Governance (Dynamic Graduation Clearance Checker)
     */
    public function test_dynamic_graduation_clearance_checker(): void
    {
        $clearanceService = app(GraduationClearanceService::class);

        // Currently student has 0 SKS -> Ineligible
        $auditInitial = $clearanceService->auditGraduation($this->mahasiswa);
        $this->assertFalse($auditInitial['is_eligible']);

        // Complete 144 SKS course with Grade A
        $mkSkripsi = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF-SKRIPSI',
            'nama_mk' => 'Tugas Akhir & Ujian Komprehensif',
            'sks_total' => 144,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasSkripsi = KelasKuliah::create([
            'id_mk' => $mkSkripsi->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'SKRIPSI-A',
            'kuota_maksimal' => 50,
            'total_terisi' => 1,
        ]);

        $krs = Krs::create([
            'id_siswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $this->dosen->id,
            'total_sks_diambil' => 144,
            'status_krs' => 'Disetujui',
        ]);

        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasSkripsi->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_angka' => 95.00,
            'nilai_akhir_huruf' => 'A',
            'bobot_mutu' => 4.00,
            'is_lulus' => true,
        ]);

        // Add 120 SKPI points (> 100 threshold)
        SkpiAktivitas::create([
            'id_siswa' => $this->mahasiswa->id,
            'kategori' => 'Prestasi & Kompetisi',
            'nama_kegiatan_id' => 'Juara 1 International Cybersecurity Contest',
            'nama_kegiatan_en' => '1st Winner International Cybersecurity Contest',
            'penyelenggara' => 'IEEE',
            'tahun_kegiatan' => 2026,
            'poin_sacs' => 120,
            'status_verifikasi' => 'Disetujui Kaprodi',
        ]);

        // Re-audit graduation
        $auditFinal = $clearanceService->auditGraduation($this->mahasiswa);
        $this->assertTrue($auditFinal['is_eligible']);

        // Approve graduation
        $graduatedMahasiswa = $clearanceService->approveGraduation($this->mahasiswa, 'IJZ-UNIV-2026-TI-0100');
        $this->assertEquals('Lulus', $graduatedMahasiswa->status_kelulusan);
        $this->assertEquals('IJZ-UNIV-2026-TI-0100', $graduatedMahasiswa->nomor_ijazah);
    }
}
