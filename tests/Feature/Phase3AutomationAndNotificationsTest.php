<?php

namespace Tests\Feature;

use App\Jobs\SendTelegramNotificationJob;
use App\Models\BapPerkuliahan;
use App\Models\Dosen;
use App\Models\EarlyWarningLog;
use App\Models\Fakultas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\PresensiKuliah;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\SuratAkademik;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Services\Academic\EarlyWarningService;
use App\Services\Document\AcademicLetterService;
use App\Services\Document\DigitalSignatureService;
use App\Services\Messaging\TelegramNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class Phase3AutomationAndNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected Siswa $goodStudent;
    protected Siswa $atRiskStudent;
    protected TahunAkademik $tahunAkademik;
    protected User $studentUser;
    protected Guru $guru;
    protected KelasKuliah $kelasKuliah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FTIK',
            'nama_fakultas' => 'Fakultas Teknik dan Informatika',
        ]);

        $prodi = ProgramStudi::create([
            'kode_prodi' => 'IF-2026',
            'nama_prodi' => 'Informatika',
            'jenjang' => 'S1',
            'id_fakultas' => $fakultas->id,
        ]);

        $kurikulum = Kurikulum::create([
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'total_sks_lulus' => 144,
            'is_active' => true,
        ]);

        $spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 5000000,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'IF-3A',
            'kompetensi_keahlian' => 'Software Engineering',
        ]);

        $this->guru = Guru::create([
            'nip' => '198501012010121001',
            'nama_guru' => 'Bambang Sudarsono, M.Kom.',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jakarta Selatan',
            'no_telp' => '081234567891',
        ]);

        $dosen = Dosen::create([
            'nidn' => '0401018501',
            'nip' => '198501012010121001',
            'nama_dosen' => 'Bambang Sudarsono, M.Kom.',
            'email' => 'bambang@campus.ac.id',
            'id_prodi' => $prodi->id,
            'is_active' => true,
        ]);

        $mataKuliah = MataKuliah::create([
            'id_kurikulum' => $kurikulum->id,
            'kode_mk' => 'IF301',
            'nama_mk' => 'Rekayasa Perangkat Lunak',
            'sks_teori' => 3,
            'sks_praktik' => 0,
            'sks_total' => 3,
            'semester_rekomendasi' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $this->kelasKuliah = KelasKuliah::create([
            'id_mk' => $mataKuliah->id,
            'id_dosen' => $dosen->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-3A',
            'kuota_maksimal' => 40,
            'total_terisi' => 2,
        ]);

        $ukt = \App\Models\Ukt::create([
            'id_prodi' => $prodi->id,
            'tahun' => 2026,
            'kelompok_ukt' => 'UKT 3',
            'nominal' => 5000000,
        ]);

        $this->goodStudent = Siswa::create([
            'nisn' => '2026000001',
            'nis' => 'G001',
            'nama' => 'Budi Pratama',
            'alamat' => 'Jl. Kampus Merdeka No. 1',
            'no_telp' => '081234567890',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
        ]);

        \App\Models\Mahasiswa::create([
            'id' => $this->goodStudent->id,
            'nim' => '2026001',
            'nisn' => '2026000001',
            'nama' => 'Budi Pratama',
            'id_prodi' => $prodi->id,
            'id_ukt' => $ukt->id,
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $this->atRiskStudent = Siswa::create([
            'nisn' => '2026000002',
            'nis' => 'R002',
            'nama' => 'Joni Terancam',
            'alamat' => 'Jl. Bahaya No. 13',
            'no_telp' => '081298765432',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
        ]);

        \App\Models\Mahasiswa::create([
            'id' => $this->atRiskStudent->id,
            'nim' => '2026002',
            'nisn' => '2026000002',
            'nama' => 'Joni Terancam',
            'id_prodi' => $prodi->id,
            'id_ukt' => $ukt->id,
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $this->studentUser = User::create([
            'name' => 'Joni Terancam',
            'email' => 'joni@campus.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'id_siswa' => $this->atRiskStudent->id,
            'telegram_chat_id' => '123456789',
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);
    }

    protected function createBap(int $pertemuanKe): BapPerkuliahan
    {
        return BapPerkuliahan::create([
            'id_kelas_kuliah' => $this->kelasKuliah->id,
            'id_guru' => $this->guru->id,
            'pertemuan_ke' => $pertemuanKe,
            'tanggal_pelaksanaan' => now()->toDateString(),
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:00',
            'materi_pembahasan' => 'Materi Kuliah Ke-' . $pertemuanKe,
            'status_verifikasi' => 'Tersubmit',
        ]);
    }

    /**
     * Test EWS calculates low risk for a high-performing student with attendance and no arrears.
     */
    public function test_ews_predictive_dropout_risk_score_low_risk_for_good_student(): void
    {
        // Give good student high attendance (10/10)
        for ($i = 1; $i <= 10; $i++) {
            $bap = $this->createBap($i);
            PresensiKuliah::create([
                'id_bap' => $bap->id,
                'id_siswa' => $this->goodStudent->id,
                'status_kehadiran' => 'Hadir',
            ]);
        }

        // High GPA
        Krs::create([
            'id_siswa' => $this->goodStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'ips_lalu' => 3.85,
            'total_sks_diambil' => 24,
            'status_krs' => 'Disetujui',
        ]);

        // Tagihan Lunas
        TagihanUkt::create([
            'id_mahasiswa' => $this->goodStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '988001002026001',
            'nomor_invoice' => 'INV-GOOD-01',
            'biaya_ukt' => 5000000,
            'total_tagihan' => 5000000,
            'total_harus_bayar' => 5000000,
            'total_sudah_bayar' => 5000000,
            'status_pembayaran' => 'Lunas',
            'tgl_jatuh_tempo' => now()->addMonth(),
        ]);

        $service = app(EarlyWarningService::class);
        $scoreResult = $service->calculatePredictiveDropoutRiskScore($this->goodStudent);

        $this->assertEquals($this->goodStudent->id, $scoreResult['siswa_id']);
        $this->assertLessThan(25, $scoreResult['total_risk_score']);
        $this->assertEquals('LOW', $scoreResult['risk_category']);
        $this->assertEquals(0.0, $scoreResult['breakdown']['tagihan']['risk_score']);
        $this->assertFalse($scoreResult['breakdown']['tagihan']['has_arrears']);
        $this->assertEquals(100.0, $scoreResult['breakdown']['presensi']['rate_percent']);
    }

    /**
     * Test EWS calculates critical/high risk for student with low attendance, low GPA, and overdue arrears.
     */
    public function test_ews_predictive_dropout_risk_score_high_or_critical_for_at_risk_student(): void
    {
        // 1 attended out of 10 meetings (10% attendance -> high presensi risk)
        $bap1 = $this->createBap(1);
        PresensiKuliah::create([
            'id_bap' => $bap1->id,
            'id_siswa' => $this->atRiskStudent->id,
            'status_kehadiran' => 'Hadir',
        ]);

        for ($i = 2; $i <= 10; $i++) {
            $bap = $this->createBap($i);
            PresensiKuliah::create([
                'id_bap' => $bap->id,
                'id_siswa' => $this->atRiskStudent->id,
                'status_kehadiran' => 'Alpa',
            ]);
        }

        // Low GPA (1.20 -> high grade risk)
        Krs::create([
            'id_siswa' => $this->atRiskStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'ips_lalu' => 1.20,
            'total_sks_diambil' => 12,
            'status_krs' => 'Disetujui',
        ]);

        // Overdue tagihan (past due date -> 30pt tagihan risk)
        TagihanUkt::create([
            'id_mahasiswa' => $this->atRiskStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '988001002026002',
            'nomor_invoice' => 'INV-RISK-01',
            'biaya_ukt' => 5000000,
            'total_tagihan' => 5000000,
            'total_harus_bayar' => 5000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->subDays(15),
        ]);

        $service = app(EarlyWarningService::class);
        $scoreResult = $service->calculatePredictiveDropoutRiskScore($this->atRiskStudent);

        $this->assertGreaterThanOrEqual(70, $scoreResult['total_risk_score']);
        $this->assertEquals('CRITICAL', $scoreResult['risk_category']);
        $this->assertEquals(30.0, $scoreResult['breakdown']['tagihan']['risk_score']);
        $this->assertTrue($scoreResult['breakdown']['tagihan']['has_arrears']);
        $this->assertEquals(10.0, $scoreResult['breakdown']['presensi']['rate_percent']);
    }

    /**
     * Test analyzeStudent automatically generates EarlyWarningLog for PREDICTIVE_DROPOUT_RISK.
     */
    public function test_ews_analyze_student_creates_predictive_dropout_log(): void
    {
        // Setup at-risk conditions
        for ($i = 1; $i <= 5; $i++) {
            $bap = $this->createBap($i);
            PresensiKuliah::create([
                'id_bap' => $bap->id,
                'id_siswa' => $this->atRiskStudent->id,
                'status_kehadiran' => 'Alpa',
            ]);
        }

        Krs::create([
            'id_siswa' => $this->atRiskStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'ips_lalu' => 1.00,
            'total_sks_diambil' => 10,
        ]);

        TagihanUkt::create([
            'id_mahasiswa' => $this->atRiskStudent->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '988001002026003',
            'nomor_invoice' => 'INV-RISK-02',
            'biaya_ukt' => 4500000,
            'total_harus_bayar' => 4500000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->subDay(),
        ]);

        $service = app(EarlyWarningService::class);
        $createdLogs = $service->analyzeStudent($this->atRiskStudent);

        $this->assertNotEmpty($createdLogs);
        $this->assertDatabaseHas('early_warning_logs', [
            'id_siswa' => $this->atRiskStudent->id,
            'trigger_type' => 'PREDICTIVE_DROPOUT_RISK',
            'is_resolved' => false,
        ]);
    }

    /**
     * Test artisan command ews:analyze with threshold and student options.
     */
    public function test_ews_analyze_artisan_command_with_student_and_threshold(): void
    {
        // 1. Run for single student
        $exitCode = Artisan::call('ews:analyze', [
            '--student' => $this->goodStudent->nisn,
        ]);
        $this->assertEquals(0, $exitCode);

        // 2. Run for all active students with threshold
        $exitCodeAll = Artisan::call('ews:analyze', [
            '--threshold' => '30',
        ]);
        $this->assertEquals(0, $exitCodeAll);
    }

    /**
     * Test DigitalSignatureService signs and verifies documents using BSRE and E_METERAI.
     */
    public function test_digital_signature_service_signing_and_verification(): void
    {
        $signatureService = app(DigitalSignatureService::class);

        $filePath = 'documents/transkrip/TRX-2026.pdf';
        $signer = [
            'name' => 'Prof. Dr. Ir. Hendra Wijaya, M.Sc.',
            'identifier' => 'NIP. 197001011995031001',
            'role' => 'Dekan Fakultas Teknik',
        ];

        // 1. Sign BSRE
        $bsreSig = $signatureService->signDocument($filePath, $signer, DigitalSignatureService::PROVIDER_BSRE);

        $this->assertTrue($bsreSig['is_valid']);
        $this->assertEquals('BSRE', $bsreSig['provider']);
        $this->assertEquals(64, strlen($bsreSig['signature_hash']));
        $this->assertNotEmpty($bsreSig['certificate_serial']);

        // Verify BSRE signature
        $verifyResult = $signatureService->verifySignature($filePath, $bsreSig['signature_hash'], 'BSRE', $signer);
        $this->assertTrue($verifyResult['is_valid']);
        $this->assertEquals('VERIFIED', $verifyResult['status']);

        // 2. Sign E_METERAI
        $meteraiSig = $signatureService->signDocument($filePath, $signer, DigitalSignatureService::PROVIDER_EMETERAI);
        $this->assertTrue($meteraiSig['is_valid']);
        $this->assertEquals('E_METERAI', $meteraiSig['provider']);
        $this->assertStringContainsString('Peruri', $meteraiSig['x509_issuer']);
    }

    /**
     * Test AcademicLetterService generates SuratMahasiswaAktif with BSrE digital signature.
     */
    public function test_academic_letter_service_generates_signed_letter_and_verifies_token(): void
    {
        $letterService = app(AcademicLetterService::class);

        $surat = $letterService->generateSuratMahasiswaAktif($this->goodStudent, 'Beasiswa Unggulan');

        $this->assertInstanceOf(SuratAkademik::class, $surat);
        $this->assertTrue($surat->isDigitallySigned());
        $this->assertEquals('BSRE', $surat->signature_provider);
        $this->assertEquals(64, strlen($surat->digital_signature_hash));
        $this->assertNotNull($surat->signed_at);

        // Verify authenticity via QR token
        $verification = $letterService->verifyLetterToken($surat->qr_verification_token);
        $this->assertNotNull($verification);
        $this->assertTrue($verification['is_valid']);
        $this->assertTrue($verification['is_digitally_signed']);
        $this->assertEquals($surat->digital_signature_hash, $verification['digital_signature_hash']);
        $this->assertEquals($this->goodStudent->nama, $verification['nama_mahasiswa']);
    }

    /**
     * Test final thesis submission in DbptaPemberkasanController applies digital signature.
     */
    public function test_dbpta_pemberkasan_submission_digitally_signs_thesis(): void
    {
        $tugasAkhir = TugasAkhir::create([
            'id_siswa' => $this->atRiskStudent->id,
            'judul_skripsi' => 'Implementasi Machine Learning untuk Prediksi Dropout Mahasiswa',
            'status_skripsi' => 'Lulus Yudisium',
            'tgl_lulus_sidang' => now(),
            'is_digitally_signed' => false,
        ]);

        $this->actingAs($this->studentUser);

        $response = $this->post(route('siakad.dbpta.pemberkasan.store'), [
            'link_berkas' => 'https://repository.campus.ac.id/thesis/final-joni-2026.pdf',
        ]);

        $response->assertRedirect(route('siakad.dbpta.pemberkasan.index'));
        $response->assertSessionHas('success');

        $tugasAkhir->refresh();
        $this->assertTrue($tugasAkhir->is_digitally_signed);
        $this->assertEquals('BSRE', $tugasAkhir->signature_provider);
        $this->assertNotEmpty($tugasAkhir->digital_signature_hash);
        $this->assertNotNull($tugasAkhir->signed_at);
        $this->assertEquals('https://repository.campus.ac.id/thesis/final-joni-2026.pdf', $tugasAkhir->file_revisi_path);
    }

    /**
     * Test TelegramNotificationService queues jobs for billing reminders, schedules, and EWS alerts.
     */
    public function test_telegram_notification_service_dispatches_jobs_for_billing_schedule_and_ews(): void
    {
        Queue::fake();

        $telegramService = app(TelegramNotificationService::class);

        // 1. Billing Reminder
        $resBilling = $telegramService->sendBillingReminder(
            $this->atRiskStudent,
            3500000,
            '30 Oktober 2026'
        );
        $this->assertTrue($resBilling);

        Queue::assertPushed(SendTelegramNotificationJob::class, function ($job) {
            return $job->chatId === '123456789'
                && str_contains($job->message, 'Tagihan Pembayaran SPP/UKT')
                && str_contains($job->message, 'Rp 3.500.000');
        });

        // 2. Schedule Alert
        $resSchedule = $telegramService->sendScheduleAlert(
            $this->atRiskStudent,
            'Kecerdasan Buatan & Soft Computing',
            'Senin, 08:00 - 10:30 WIB',
            'Lab Komputer 3'
        );
        $this->assertTrue($resSchedule);

        Queue::assertPushed(SendTelegramNotificationJob::class, function ($job) {
            return $job->chatId === '123456789'
                && str_contains($job->message, 'Kecerdasan Buatan')
                && str_contains($job->message, 'Lab Komputer 3');
        });

        // 3. EWS Risk Alert
        $resEws = $telegramService->sendEwsRiskAlert(
            $this->atRiskStudent,
            85,
            'CRITICAL'
        );
        $this->assertTrue($resEws);

        Queue::assertPushed(SendTelegramNotificationJob::class, function ($job) {
            return $job->chatId === '123456789'
                && str_contains($job->message, 'Early Warning System (EWS)')
                && str_contains($job->message, '85 / 100')
                && str_contains($job->message, 'KRITIS');
        });
    }

    /**
     * Test SendTelegramNotificationJob sends HTTP request to Telegram Bot API.
     */
    public function test_send_telegram_notification_job_sends_http_request_to_telegram_api(): void
    {
        config(['services.telegram.bot_token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11']);

        Http::fake([
            'https://api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 999,
                    'chat' => ['id' => 123456789],
                    'text' => 'Pesan Percobaan',
                ],
            ], 200),
        ]);

        $job = new SendTelegramNotificationJob('123456789', '<b>Pesan Percobaan</b>', 'HTML');
        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11/sendMessage'
                && $request['chat_id'] === '123456789'
                && $request['text'] === '<b>Pesan Percobaan</b>'
                && $request['parse_mode'] === 'HTML';
        });
    }
}
