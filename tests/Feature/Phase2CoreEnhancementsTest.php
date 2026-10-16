<?php

namespace Tests\Feature;

use App\Filament\Parent\Resources\NilaiObeResource;
use App\Filament\Parent\Resources\PresensiMahasiswaResource;
use App\Filament\Parent\Resources\TagihanUktResource;
use App\Models\BankMitra;
use App\Models\BapPerkuliahan;
use App\Models\Cpmk;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\NilaiObe;
use App\Models\PresensiMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TagihanUkt;
use App\Models\TagihanVa;
use App\Models\TahunAkademik;
use App\Models\TransaksiH2h;
use App\Models\User;
use App\Services\Finance\H2hBillingService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2CoreEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected Mahasiswa $mahasiswa;
    protected Mahasiswa $otherMahasiswa;
    protected Siswa $siswa;
    protected Siswa $otherSiswa;
    protected ProgramStudi $prodi;
    protected User $parentUser;
    protected User $otherParentUser;
    protected TahunAkademik $tahunAkademik;
    protected BankMitra $midtransBank;
    protected BankMitra $xenditBank;

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
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Informatika',
            'jenjang' => 'S1',
        ]);

        $this->kurikulum = \App\Models\Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'total_sks_lulus' => 144,
            'is_active' => true,
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'IF-1', 'kompetensi_keahlian' => 'RPL']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 250000]);

        $this->siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '230001001',
            'nama' => 'Aditya Pratama',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Kampus No. 1',
            'no_telp' => '081234567890',
            'id_spp' => $spp->id,
        ]);

        $this->otherSiswa = Siswa::create([
            'nisn' => '0051234568',
            'nis' => '230001002',
            'nama' => 'Siti Nurhaliza',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Kampus No. 2',
            'no_telp' => '081234567891',
            'id_spp' => $spp->id,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nim' => '230001001',
            'nisn' => '0051234567',
            'nama' => 'Aditya Pratama',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Belum Lulus',
        ]);

        $this->otherMahasiswa = Mahasiswa::create([
            'nim' => '230001002',
            'nisn' => '0051234568',
            'nama' => 'Siti Nurhaliza',
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Belum Lulus',
        ]);

        $this->parentUser = User::create([
            'name' => 'Wali Aditya',
            'email' => 'wali.aditya@example.com',
            'password' => bcrypt('password'),
            'role' => 'wali',
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_siswa' => $this->siswa->id,
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $this->otherParentUser = User::create([
            'name' => 'Wali Siti',
            'email' => 'wali.siti@example.com',
            'password' => bcrypt('password'),
            'role' => 'wali',
            'id_mahasiswa' => $this->otherMahasiswa->id,
            'id_siswa' => $this->otherSiswa->id,
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $this->midtransBank = BankMitra::create([
            'kode_bank' => 'MIDTRANS',
            'nama_bank' => 'Midtrans Payment Gateway',
            'gateway_provider' => 'MIDTRANS',
            'prefix_va' => '988',
            'secret_key' => 'midtrans-secret',
            'server_key' => 'SB-Mid-server-TESTKEY123',
            'environment' => 'sandbox',
            'is_active' => true,
        ]);

        $this->xenditBank = BankMitra::create([
            'kode_bank' => 'XENDIT',
            'nama_bank' => 'Xendit Payment Gateway',
            'gateway_provider' => 'XENDIT',
            'prefix_va' => '887',
            'secret_key' => 'xendit-callback-token-secret-123',
            'server_key' => 'xnd_development_TESTKEY456',
            'environment' => 'sandbox',
            'is_active' => true,
        ]);
    }

    /**
     * Test BankMitra payment gateway helpers
     */
    public function test_bank_mitra_gateway_attributes_and_helpers(): void
    {
        $this->assertTrue($this->midtransBank->isPaymentGateway());
        $this->assertTrue($this->midtransBank->isMidtrans());
        $this->assertFalse($this->midtransBank->isXendit());

        $this->assertTrue($this->xenditBank->isPaymentGateway());
        $this->assertTrue($this->xenditBank->isXendit());
        $this->assertFalse($this->xenditBank->isMidtrans());
    }

    /**
     * Test Midtrans webhook reconciliation in H2hBillingService
     */
    public function test_midtrans_webhook_reconciliation(): void
    {
        $tagihanUkt = TagihanUkt::create([
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_invoice' => 'INV-UKT-20261-001',
            'nomor_va' => '9880051234567',
            'total_tagihan' => 3500000,
            'total_potongan_beasiswa' => 0,
            'total_harus_bayar' => 3500000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        $orderId = 'INV-UKT-20261-001';
        $statusCode = '200';
        $grossAmount = '3500000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->midtransBank->server_key);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'transaction_id' => 'MID-TRX-998877',
            'payment_type' => 'gopay',
        ];

        $billingService = app(H2hBillingService::class);
        $transaksi = $billingService->processMidtransWebhook($payload);

        $this->assertInstanceOf(TransaksiH2h::class, $transaksi);
        $this->assertEquals('MID-TRX-998877', $transaksi->nomor_transaksi_bank);
        $this->assertEquals(3500000, $transaksi->jumlah_dibayar);
        $this->assertEquals('EWALLET_GOPAY', $transaksi->channel_bayar);

        $tagihanUkt->refresh();
        $this->assertEquals('Lunas', $tagihanUkt->status_pembayaran);

        // Verification of KRS Financial Clearance unlocked
        $this->assertDatabaseHas('financial_clearances', [
            'id_siswa' => $this->mahasiswa->id,
            'is_krs_unlocked' => true,
        ]);

        // Audit Trail recorded
        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'GATEWAY_PAYMENT_SUCCESS',
        ]);
    }

    /**
     * Test Xendit webhook reconciliation in H2hBillingService
     */
    public function test_xendit_webhook_reconciliation(): void
    {
        $tagihanVa = TagihanVa::create([
            'id_siswa' => $this->siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_bank_mitra' => $this->xenditBank->id,
            'nomor_va' => '8870051234567',
            'nomor_invoice' => 'XND-INV-20261-002',
            'total_tagihan' => 2000000,
            'total_harus_bayar' => 2000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        $payload = [
            'id' => 'xnd_cb_trx_554433',
            'external_id' => '8870051234567',
            'amount' => 2000000,
            'status' => 'PAID',
            'payment_method' => 'QRIS',
        ];

        $billingService = app(H2hBillingService::class);
        $transaksi = $billingService->processXenditWebhook($payload, $this->xenditBank->secret_key);

        $this->assertEquals('xnd_cb_trx_554433', $transaksi->nomor_transaksi_bank);
        $this->assertEquals('XENDIT_QRIS', $transaksi->channel_bayar);

        $tagihanVa->refresh();
        $this->assertEquals('Lunas', $tagihanVa->status_pembayaran);
    }

    /**
     * Test Payment Gateway API Webhook Endpoints
     */
    public function test_payment_gateway_api_endpoints(): void
    {
        $tagihanUkt = TagihanUkt::create([
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_invoice' => 'INV-API-ENDPOINT-001',
            'nomor_va' => '9881122334455',
            'total_tagihan' => 1500000,
            'total_harus_bayar' => 1500000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        $orderId = 'INV-API-ENDPOINT-001';
        $statusCode = '200';
        $grossAmount = '1500000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->midtransBank->server_key);

        // 1. Midtrans endpoint
        $responseMidtrans = $this->postJson(route('api.payment-gateway.midtrans'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'transaction_id' => 'MID-API-1122',
            'payment_type' => 'bank_transfer',
            'va_numbers' => [['bank' => 'bni']],
        ]);

        $responseMidtrans->assertStatus(200);
        $responseMidtrans->assertJsonPath('data.reconciled', true);

        // 2. Xendit endpoint
        $tagihanXnd = TagihanVa::create([
            'id_siswa' => $this->otherSiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '8879988776655',
            'nomor_invoice' => 'XND-API-002',
            'total_tagihan' => 1000000,
            'total_harus_bayar' => 1000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        $responseXendit = $this->withHeaders([
            'x-callback-token' => $this->xenditBank->secret_key,
        ])->postJson(route('api.payment-gateway.xendit'), [
            'id' => 'xnd_api_9988',
            'external_id' => '8879988776655',
            'amount' => 1000000,
            'status' => 'PAID',
            'payment_method' => 'OVO',
        ]);

        $responseXendit->assertStatus(200);
        $responseXendit->assertJsonPath('data.reconciled', true);
    }

    /**
     * Test Parent Portal Role & Panel Authorization
     */
    public function test_parent_portal_role_and_panel_authorization(): void
    {
        $this->assertTrue($this->parentUser->isWali());
        $this->assertTrue($this->parentUser->isParent());

        $parentPanel = Filament::getPanel('parent');
        $adminPanel = Filament::getPanel('admin');

        // Wali can access parent panel
        $this->assertTrue($this->parentUser->canAccessPanel($parentPanel));
        // Wali CANNOT access admin panel
        $this->assertFalse($this->parentUser->canAccessPanel($adminPanel));

        // Authenticated Parent can access /wali
        $response = $this->actingAs($this->parentUser)->get('/wali');
        $response->assertStatus(200);
    }

    /**
     * Test Parent Portal TagihanUkt Scoping & Read-Only Enforcement
     */
    public function test_parent_portal_tagihan_ukt_read_only_and_scoping(): void
    {
        TagihanUkt::create([
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_invoice' => 'INV-WALI-01',
            'nomor_va' => '9880000001',
            'total_tagihan' => 3000000,
            'total_harus_bayar' => 3000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        TagihanUkt::create([
            'id_mahasiswa' => $this->otherMahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_invoice' => 'INV-WALI-02',
            'nomor_va' => '9880000002',
            'total_tagihan' => 3000000,
            'total_harus_bayar' => 3000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addDays(30),
        ]);

        // Strictly read-only checks
        $this->assertFalse(TagihanUktResource::canCreate());
        $tagihanRecord = TagihanUkt::first();
        $this->assertFalse(TagihanUktResource::canEdit($tagihanRecord));
        $this->assertFalse(TagihanUktResource::canDelete($tagihanRecord));

        // Scoping check for parent 1
        $this->actingAs($this->parentUser);
        $scopedQuery = TagihanUktResource::getEloquentQuery();
        $results = $scopedQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($this->mahasiswa->id, $results->first()->id_mahasiswa);
    }

    /**
     * Test Parent Portal NilaiObe Scoping & Read-Only Enforcement
     */
    public function test_parent_portal_nilai_obe_read_only_and_scoping(): void
    {
        $dosen = Dosen::create([
            'nidn' => '0011223344',
            'nip' => '198501012010011001',
            'nama_dosen' => 'Dr. Hendra, M.Kom',
            'id_prodi' => $this->prodi->id,
            'email' => 'hendra@campus.ac.id',
            'no_telp' => '081299990000',
            'is_active' => true,
        ]);

        $mataKuliah = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF201',
            'nama_mk' => 'Struktur Data & Algoritma',
            'sks_teori' => 3,
            'sks_praktik' => 0,
            'sks_total' => 3,
            'semester_rekomendasi' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mataKuliah->id,
            'id_dosen' => $dosen->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-3A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $cpl = \App\Models\Cpl::create([
            'id_prodi' => $this->prodi->id,
            'kode_cpl' => 'CPL-01',
            'aspek' => 'Keterampilan Khusus',
            'deskripsi_cpl_id' => 'Mampu merancang struktur data efisien',
        ]);

        $cpmk = Cpmk::create([
            'id_mk' => $mataKuliah->id,
            'id_cpl' => $cpl->id,
            'kode_cpmk' => 'CPMK-1',
            'deskripsi_cpmk_id' => 'Mampu mengimplementasikan Binary Search Tree',
            'bobot_persentase' => 30,
        ]);

        // Child 1 KRS
        $krsChild = Krs::create([
            'id_siswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'total_sks' => 3,
            'status_persetujuan' => 'Disetujui',
        ]);

        $krsDetailChild = KrsDetail::create([
            'id_krs' => $krsChild->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
        ]);

        $nilaiObeChild = NilaiObe::create([
            'id_krs_detail' => $krsDetailChild->id,
            'id_cpmk' => $cpmk->id,
            'skor_pencapaian' => 88.50,
            'is_terpenuhi' => true,
        ]);

        // Child 2 KRS
        $krsOther = Krs::create([
            'id_siswa' => $this->otherMahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'total_sks' => 3,
            'status_persetujuan' => 'Disetujui',
        ]);

        $krsDetailOther = KrsDetail::create([
            'id_krs' => $krsOther->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'status_ambil' => 'Baru',
        ]);

        NilaiObe::create([
            'id_krs_detail' => $krsDetailOther->id,
            'id_cpmk' => $cpmk->id,
            'skor_pencapaian' => 70.00,
            'is_terpenuhi' => true,
        ]);

        // Strictly read-only checks
        $this->assertFalse(NilaiObeResource::canCreate());
        $this->assertFalse(NilaiObeResource::canEdit($nilaiObeChild));
        $this->assertFalse(NilaiObeResource::canDelete($nilaiObeChild));

        // Scoping check for parent 1
        $this->actingAs($this->parentUser);
        $scopedQuery = NilaiObeResource::getEloquentQuery();
        $results = $scopedQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($nilaiObeChild->id, $results->first()->id);
    }

    /**
     * Test Parent Portal PresensiMahasiswa Scoping & Read-Only Enforcement
     */
    public function test_parent_portal_presensi_mahasiswa_read_only_and_scoping(): void
    {
        $dosen = Dosen::create([
            'nidn' => '0011228899',
            'nip' => '198701012010011002',
            'nama_dosen' => 'Rina S., M.T',
            'id_prodi' => $this->prodi->id,
            'email' => 'rina@campus.ac.id',
            'no_telp' => '081299991111',
            'is_active' => true,
        ]);

        $mataKuliah = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF202',
            'nama_mk' => 'Basis Data',
            'sks_teori' => 3,
            'sks_praktik' => 0,
            'sks_total' => 3,
            'semester_rekomendasi' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mataKuliah->id,
            'id_dosen' => $dosen->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-3B',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $guru = \App\Models\Guru::create([
            'nip' => '19870101',
            'nama_guru' => 'Rina S., M.T',
            'jenis_kelamin' => 'P',
            'alamat' => 'Bandung',
            'no_telp' => '0812345678',
        ]);

        $bap = BapPerkuliahan::create([
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_guru' => $guru->id,
            'pertemuan_ke' => 1,
            'tanggal_pelaksanaan' => now()->toDateString(),
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:00',
            'materi_pembahasan' => 'Pengenalan SQL DDL',
            'status_verifikasi' => 'Diverifikasi BAAK',
        ]);

        // Attendance records
        $presensiChild = PresensiMahasiswa::create([
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_bap' => $bap->id,
            'status' => 'Hadir',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'waktu_hadir' => now(),
        ]);

        PresensiMahasiswa::create([
            'id_mahasiswa' => $this->otherMahasiswa->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'id_bap' => $bap->id,
            'status' => 'Izin',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'waktu_hadir' => now(),
        ]);

        // Strictly read-only checks
        $this->assertFalse(PresensiMahasiswaResource::canCreate());
        $this->assertFalse(PresensiMahasiswaResource::canEdit($presensiChild));
        $this->assertFalse(PresensiMahasiswaResource::canDelete($presensiChild));

        // Scoping check for parent 1
        $this->actingAs($this->parentUser);
        $scopedQuery = PresensiMahasiswaResource::getEloquentQuery();
        $results = $scopedQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($presensiChild->id, $results->first()->id);
    }
}
