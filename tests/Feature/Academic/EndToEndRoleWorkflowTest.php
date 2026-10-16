<?php

namespace Tests\Feature\Academic;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndToEndRoleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $dosenUser;
    protected Dosen $dosen;
    protected User $studentUser;
    protected Mahasiswa $mahasiswa;
    protected Siswa $siswa;
    protected User $otherStudentUser;
    protected Siswa $otherSiswa;
    protected User $petugasUser;
    protected User $waliUser;
    protected TahunAkademik $tahunAkademik;
    protected Kelas $kelas;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // Master Faculty & Study Program
        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI-1A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 3500000,
        ]);

        // 1. Admin User
        $this->adminUser = User::factory()->create([
            'name' => 'Admin E2E',
            'role' => 'admin',
        ]);

        // 2. Dosen User & Model
        $this->dosen = Dosen::create([
            'id_prodi' => $prodi->id,
            'nidn' => '0011223344',
            'nama_dosen' => 'Dr. Budi Santoso, M.Kom',
            'email' => 'budi.santoso@univ.ac.id',
            'is_active' => true,
        ]);

        $this->dosenUser = User::factory()->create([
            'name' => 'Dosen E2E',
            'role' => 'dosen',
            'id_dosen' => $this->dosen->id,
        ]);

        // 3. Student User & Polymorphic Models
        $this->mahasiswa = Mahasiswa::create([
            'id_prodi' => $prodi->id,
            'nim' => '2026001001',
            'nama' => 'Ahmad Fauzi',
            'angkatan' => 2026,
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0011223344',
            'nis' => '2026001001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Kampus 1',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);

        $this->studentUser = User::factory()->create([
            'name' => 'Student E2E',
            'role' => 'mahasiswa',
            'id_siswa' => $this->siswa->id,
        ]);

        // 4. Other Student User (for IDOR tests)
        $this->otherSiswa = Siswa::create([
            'nisn' => '0011223355',
            'nis' => '2026001002',
            'nama' => 'Citra Lestari',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Kampus 2',
            'no_telp' => '081234567891',
            'id_spp' => $this->spp->id,
        ]);

        $this->otherStudentUser = User::factory()->create([
            'name' => 'Other Student E2E',
            'role' => 'mahasiswa',
            'id_siswa' => $this->otherSiswa->id,
        ]);

        // 5. Petugas (Cashier) User
        $this->petugasUser = User::factory()->create([
            'name' => 'Petugas E2E',
            'role' => 'petugas',
        ]);

        // 6. Wali Murid (Parent) User
        $this->waliUser = User::factory()->create([
            'name' => 'Wali E2E',
            'role' => 'wali',
        ]);
    }

    /**
     * Role Journey 1: Public / Unauthenticated User
     */
    public function test_public_user_journey_and_boundaries(): void
    {
        // 1. Landing page redirects to login
        $response = $this->get('/');
        $response->assertRedirect(route('login'));

        // 2. Login page is directly accessible
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // 3. Public payment lookup form is accessible
        $response = $this->get(route('cek.index'));
        $response->assertStatus(200);

        // 4. Protected student dashboard redirects unauthenticated visitor to login
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        // 5. Protected admin panel redirects unauthenticated visitor
        $response = $this->get('/admin');
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    /**
     * Role Journey 2: Mahasiswa (Student) Portal Workflows & Anti-IDOR Protections
     */
    public function test_mahasiswa_user_journey_and_anti_idor_boundaries(): void
    {
        // 1. Dashboard access
        $response = $this->actingAs($this->studentUser)->get('/dashboard');
        $response->assertStatus(200);

        // 2. Academic portal views
        $response = $this->actingAs($this->studentUser)->get(route('siakad.krs.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->studentUser)->get(route('web.nilai.khs', $this->siswa->id));
        $response->assertStatus(200);

        $response = $this->actingAs($this->studentUser)->get(route('siakad.esurat.index'));
        $response->assertStatus(200);

        // 3. Anti-IDOR protection: Student cannot access another student's kwitansi
        $otherPayment = Pembayaran::create([
            'id_petugas' => $this->petugasUser->id,
            'id_siswa' => $this->otherSiswa->id,
            'tgl_bayar' => now(),
            'bulan_dibayar' => 'Januari',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 500000,
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('web.pembayaran.cetak', $otherPayment->id));
        $response->assertStatus(403);

        // 4. Role boundary: Student cannot access admin user management
        $response = $this->actingAs($this->studentUser)->get(route('web.users.index'));
        $response->assertStatus(403);
    }

    /**
     * Role Journey 3: Dosen (Lecturer & Academic Advisor)
     */
    public function test_dosen_user_journey_and_role_boundaries(): void
    {
        // 1. Lecturer dashboard
        $response = $this->actingAs($this->dosenUser)->get('/dashboard');
        $response->assertStatus(200);

        // 2. Lecturer BAP
        $response = $this->actingAs($this->dosenUser)->get(route('siakad.dosen.bap.index'));
        $response->assertStatus(200);

        // 3. KRS Approval page
        $response = $this->actingAs($this->dosenUser)->get(route('siakad.dosen.krs.index'));
        $response->assertStatus(200);

        // 4. Role boundary: Lecturer cannot access admin user management
        $response = $this->actingAs($this->dosenUser)->get(route('web.users.index'));
        $response->assertStatus(403);
    }

    /**
     * Role Journey 4: Petugas (Finance Operator / Cashier)
     */
    public function test_petugas_user_journey_and_financial_boundaries(): void
    {
        // 1. Petugas can access payment ledger
        $response = $this->actingAs($this->petugasUser)->get(route('web.pembayaran.index'));
        $response->assertStatus(200);

        // 2. Petugas can access payment creation
        $response = $this->actingAs($this->petugasUser)->get(route('web.pembayaran.create'));
        $response->assertStatus(200);

        // 3. Role boundary: Petugas cannot access student SKPI print without student profile
        $response = $this->actingAs($this->petugasUser)->get('/siakad/skpi/print');
        $response->assertStatus(403);
    }

    /**
     * Role Journey 5: Admin (BAAK / System Administrator)
     */
    public function test_admin_user_journey_across_master_modules(): void
    {
        // 1. Student master catalog
        $response = $this->actingAs($this->adminUser)->get(route('web.mahasiswa.index'));
        $response->assertStatus(200);

        // 2. Lecturer master catalog
        $response = $this->actingAs($this->adminUser)->get(route('web.dosen.index'));
        $response->assertStatus(200);

        // 3. Course master catalog
        $response = $this->actingAs($this->adminUser)->get(route('web.matakuliah.index'));
        $response->assertStatus(200);

        // 4. UKT master catalog
        $response = $this->actingAs($this->adminUser)->get(route('web.ukt.index'));
        $response->assertStatus(200);

        // 5. Early Warning System
        $response = $this->actingAs($this->adminUser)->get(route('siakad.baak.ews.index'));
        $response->assertStatus(200);
    }

    /**
     * Role Journey 6: Wali Murid (Parent Portal Authorization & Isolation)
     */
    public function test_wali_user_journey_and_isolation(): void
    {
        // 1. Wali can access Wali panel
        $response = $this->actingAs($this->waliUser)->get('/wali');
        $response->assertStatus(200);

        // 2. Wali is strictly barred from accessing Admin panel
        $response = $this->actingAs($this->waliUser)->get('/admin');
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }
}
