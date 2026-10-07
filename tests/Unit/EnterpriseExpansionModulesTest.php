<?php

namespace Tests\Unit;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Services\Academic\YudisiumPredicateService;
use App\Services\Document\AcademicLetterService;
use App\Services\Finance\ScholarshipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseExpansionModulesTest extends TestCase
{
    use RefreshDatabase;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'IF-4A', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 3000000]);

        $this->siswa = Siswa::create([
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'alamat' => 'Bekasi',
            'no_telp' => '085811362629',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);
    }

    public function test_academic_letter_generation_and_verification(): void
    {
        $service = new AcademicLetterService();

        $surat = $service->generateSuratMahasiswaAktif($this->siswa, 'Persyaratan KIP-Kuliah');

        $this->assertNotNull($surat->qr_verification_token);
        $this->assertEquals('Surat Keterangan Mahasiswa Aktif', $surat->jenis_surat);
        $this->assertStringContainsString('SKMA/', $surat->nomor_surat);

        // Test token verification
        $verification = $service->verifyLetterToken($surat->qr_verification_token);
        $this->assertTrue($verification['is_valid']);
        $this->assertEquals('MUHAMMAD LUAYYI ATHOILLAH', $verification['nama_mahasiswa']);
        $this->assertEquals('109240940090', $verification['nim_nisn']);
    }

    public function test_yudisium_predicate_calculation(): void
    {
        $service = new YudisiumPredicateService();

        // 1. Cum Laude (IPK 3.85, 8 semester, tanpa mengulang)
        $cumLaude = $service->calculatePredicate(3.85, 8, false);
        $this->assertEquals('Dengan Pujian (Cum Laude)', $cumLaude['predikat']);
        $this->assertTrue($cumLaude['eligible_cum_laude']);

        // 2. Sangat Memuaskan (IPK 3.85 tapi 9 semester)
        $sangatMemuaskanOvertime = $service->calculatePredicate(3.85, 9, false);
        $this->assertEquals('Sangat Memuaskan', $sangatMemuaskanOvertime['predikat']);
        $this->assertFalse($sangatMemuaskanOvertime['eligible_cum_laude']);

        // 3. Memuaskan (IPK 2.90)
        $memuaskan = $service->calculatePredicate(2.90, 8, false);
        $this->assertEquals('Memuaskan', $memuaskan['predikat']);
    }

    public function test_scholarship_creation_and_billing_discount(): void
    {
        $service = new ScholarshipService();

        // 1. Create KIP-K 100% Scholarship
        $kipk = $service->createScholarship([
            'nama_beasiswa' => 'Beasiswa KIP-Kuliah 2026',
            'penyelenggara' => 'Kemendikbudristek',
            'jenis_cakupan' => 'FULL',
            'persentase_potongan' => 100.00,
        ]);

        // 2. Apply for student
        $service->applyScholarship($this->siswa, $kipk->id, 3.75, 'Penerima KIP-K Kuota Utama');

        // 3. Calculate Net Billing on 3.000.000 UKT
        $netBilling = $service->calculateNetBillingAmount($this->siswa, 3000000.00);
        $this->assertEquals(0.00, $netBilling);

        // 4. Test Partial Discount (50%)
        $parsial = $service->createScholarship([
            'nama_beasiswa' => 'Beasiswa Prestasi 50%',
            'penyelenggara' => 'Yayasan Kampus',
            'jenis_cakupan' => 'PARSIAL',
            'persentase_potongan' => 50.00,
        ]);

        // Switch to partial scholarship
        $siswa2 = Siswa::create([
            'nama' => 'MAHASISWA PRESTASI',
            'nisn' => '109240940091',
            'nis' => '109240940091',
            'alamat' => 'Jakarta',
            'no_telp' => '081234567890',
            'id_kelas' => $this->siswa->id_kelas,
            'id_spp' => $this->siswa->id_spp,
        ]);

        $service->applyScholarship($siswa2, $parsial->id, 3.90);
        $netBilling2 = $service->calculateNetBillingAmount($siswa2, 3000000.00);
        $this->assertEquals(1500000.00, $netBilling2);
    }
}
