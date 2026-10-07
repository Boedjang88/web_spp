<?php

namespace Tests\Feature;

use App\Models\BankMitra;
use App\Models\FinancialClearance;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\MataKuliahPrasyarat;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\Academic\GradingService;
use App\Services\Academic\SchedulingService;
use App\Services\Academic\SmartKrsService;
use App\Services\Finance\H2hBillingService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseSiakadTest extends TestCase
{
    use RefreshDatabase;

    protected Fakultas $fakultas;
    protected ProgramStudi $prodi;
    protected TahunAkademik $tahunAkademik;
    protected Kurikulum $kurikulum;
    protected MataKuliah $mkDasar;
    protected MataKuliah $mkLanjut;
    protected KelasKuliah $kelasA;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected User $userSiswa1;
    protected BankMitra $bankMitra;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $this->fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Informatika',
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->mkDasar = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF101',
            'nama_mk' => 'Algoritma Pemrograman',
            'sks_total' => 3,
        ]);

        $this->mkLanjut = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF201',
            'nama_mk' => 'Struktur Data Lanjut',
            'sks_total' => 3,
        ]);

        // IF201 requires IF101
        MataKuliahPrasyarat::create([
            'id_mk' => $this->mkLanjut->id,
            'id_mk_prasyarat' => $this->mkDasar->id,
        ]);

        // Class with 1 Seat Capacity limit for testing concurrency
        $this->kelasA = KelasKuliah::create([
            'id_mk' => $this->mkDasar->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'A',
            'kuota_maksimal' => 1,
            'total_terisi' => 0,
        ]);

        $dummyKelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'RPL']);
        $dummySpp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);

        $this->siswa1 = Siswa::create([
            'nisn' => '1111111111',
            'nis' => '1001',
            'nama' => 'Mahasiswa Satu',
            'id_kelas' => $dummyKelas->id,
            'id_spp' => $dummySpp->id,
            'alamat' => 'Bandung',
            'no_telp' => '0811111111',
        ]);

        $this->siswa2 = Siswa::create([
            'nisn' => '2222222222',
            'nis' => '1002',
            'nama' => 'Mahasiswa Dua',
            'id_kelas' => $dummyKelas->id,
            'id_spp' => $dummySpp->id,
            'alamat' => 'Jakarta',
            'no_telp' => '0822222222',
        ]);

        $this->userSiswa1 = User::factory()->create([
            'name' => 'Mahasiswa Satu',
            'email' => 'mhs1@test.com',
            'role' => 'siswa',
            'id_siswa' => $this->siswa1->id,
            'is_active' => true,
        ]);

        $this->bankMitra = BankMitra::create([
            'kode_bank' => 'BNI',
            'nama_bank' => 'Bank Negara Indonesia',
            'prefix_va' => '988',
            'secret_key' => 'secret_enterprise_bni_2026',
        ]);
    }

    public function test_krs_blocks_enrollment_if_financial_clearance_locked(): void
    {
        $krsService = app(SmartKrsService::class);

        // Ensure financial clearance is locked
        FinancialClearance::updateOrCreate(
            ['id_siswa' => $this->siswa1->id, 'id_tahun_akademik' => $this->tahunAkademik->id],
            ['is_krs_unlocked' => false]
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('KRS Terkunci');

        $krsService->enrollClassWithPessimisticLock(
            $this->siswa1->id,
            $this->kelasA->id,
            $this->tahunAkademik->id
        );
    }

    public function test_h2h_bank_webhook_processes_payment_and_unlocks_krs_instantly(): void
    {
        $billingService = app(H2hBillingService::class);

        // 1. Generate Semester Invoice
        $tagihan = $billingService->generateSemesterInvoice(
            $this->siswa1->id,
            $this->tahunAkademik->id,
            $this->bankMitra->id,
            [['nama_item' => 'UKT Semester Ganjil', 'nominal' => 4500000]]
        );

        $this->assertEquals('Belum Bayar', $tagihan->status_pembayaran);

        // Verify KRS is initially locked
        $clearance = FinancialClearance::where('id_siswa', $this->siswa1->id)->first();
        $this->assertFalse((bool) ($clearance?->is_krs_unlocked ?? false));

        // 2. Bank Webhook Callback Payload
        $response = $this->postJson('/api/h2h/webhook', [
            'nomor_va' => $tagihan->nomor_va,
            'nomor_transaksi_bank' => 'TRX-BNI-' . time(),
            'jumlah_bayar' => 4500000,
            'kode_bank' => 'BNI',
            'channel_bayar' => 'Mobile Banking',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'SUCCESS')
            ->assertJsonPath('data.krs_unlocked', true);

        // Verify KRS is now unlocked
        $clearance = FinancialClearance::where('id_siswa', $this->siswa1->id)->first();
        $this->assertNotNull($clearance);
        $this->assertTrue($clearance->is_krs_unlocked);

        $tagihan->refresh();
        $this->assertEquals('Lunas', $tagihan->status_pembayaran);
    }

    public function test_krs_pessimistic_lock_enforces_exact_capacity_under_contention(): void
    {
        $krsService = app(SmartKrsService::class);

        // Unlock financial clearance for both students
        FinancialClearance::updateOrCreate(
            ['id_siswa' => $this->siswa1->id, 'id_tahun_akademik' => $this->tahunAkademik->id],
            ['is_krs_unlocked' => true]
        );
        FinancialClearance::updateOrCreate(
            ['id_siswa' => $this->siswa2->id, 'id_tahun_akademik' => $this->tahunAkademik->id],
            ['is_krs_unlocked' => true]
        );

        // Student 1 grabs the only 1 seat
        $detail1 = $krsService->enrollClassWithPessimisticLock(
            $this->siswa1->id,
            $this->kelasA->id,
            $this->tahunAkademik->id
        );

        $this->assertNotNull($detail1);
        $this->kelasA->refresh();
        $this->assertEquals(1, $this->kelasA->total_terisi);

        // Student 2 attempts to enroll in the full class (Capacity = 1, Enrolled = 1)
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('sudah penuh');

        $krsService->enrollClassWithPessimisticLock(
            $this->siswa2->id,
            $this->kelasA->id,
            $this->tahunAkademik->id
        );
    }

    public function test_grading_service_computes_multi_component_scores_and_letter_grade(): void
    {
        $gradingService = app(GradingService::class);

        // 88.00 -> Grade A (4.00)
        [$hurufA, $mutuA, $lulusA] = $gradingService->convertScoreToGrade(88.50);
        $this->assertEquals('A', $hurufA);
        $this->assertEquals(4.00, $mutuA);
        $this->assertTrue($lulusA);

        // 72.00 -> Grade B (3.00)
        [$hurufB, $mutuB, $lulusB] = $gradingService->convertScoreToGrade(72.00);
        $this->assertEquals('B', $hurufB);
        $this->assertEquals(3.00, $mutuB);
        $this->assertTrue($lulusB);

        // 40.00 -> Grade E (0.00)
        [$hurufE, $mutuE, $lulusE] = $gradingService->convertScoreToGrade(40.00);
        $this->assertEquals('E', $hurufE);
        $this->assertEquals(0.00, $mutuE);
        $this->assertFalse($lulusE);
    }

    public function test_scheduling_service_detects_room_capacity_and_clash(): void
    {
        $schedService = app(SchedulingService::class);

        $gedung = Gedung::create(['kode_gedung' => 'G1', 'nama_gedung' => 'Gedung Rektorat']);
        $ruanganKecil = Ruangan::create([
            'id_gedung' => $gedung->id,
            'kode_ruangan' => 'R-LAB-01',
            'nama_ruangan' => 'Lab Komputer 01',
            'kapasitas' => 20,
        ]);

        $guru = Guru::create([
            'nip' => '198801012015011001',
            'nama_guru' => 'Dr. Hendra Kusuma, M.T.',
            'jenis_kelamin' => 'L',
        ]);

        // Class with 40 students in a 20-seat room -> Conflict
        $kelasBesar = KelasKuliah::create([
            'id_mk' => $this->mkDasar->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'B',
            'kuota_maksimal' => 40,
        ]);

        $result = $schedService->validateScheduleConflict(
            $kelasBesar->id,
            $ruanganKecil->id,
            $guru->id,
            'Senin',
            '08:00',
            '10:30'
        );

        $this->assertTrue($result['has_conflict']);
        $this->assertEquals('ROOM_CAPACITY_EXCEEDED', $result['conflict_type']);
    }
}
