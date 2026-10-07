<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Facility;
use App\Models\Fakultas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Enterprise9ModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Siswa $student;
    protected User $dosenUser;
    protected Guru $guru;
    protected Dosen $dosen;
    protected TahunAkademik $activeTa;
    protected Kelas $kelas;
    protected ProgramStudi $prodi;
    protected Kurikulum $kurikulum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeTa = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => '2026-09-01',
            'tgl_selesai' => '2027-02-28',
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
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 5000000,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI-2024-A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $this->studentUser = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@test.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'consent_pdp_at' => now(),
        ]);

        $this->student = Siswa::create([
            'user_id' => $this->studentUser->id,
            'nisn' => '2026001001',
            'nis' => '1001',
            'nama' => 'Mahasiswa Test',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Kampus I',
            'no_telp' => '081234567890',
        ]);

        $this->studentUser->update(['id_siswa' => $this->student->id]);

        $this->dosenUser = User::create([
            'name' => 'Dr. Dosen Test',
            'email' => 'dosen@test.ac.id',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'consent_pdp_at' => now(),
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->dosenUser->id,
            'nip' => '198501012026011001',
            'nama_guru' => 'Dr. Dosen Test',
            'mapel_id' => 1,
        ]);

        $this->dosen = Dosen::create([
            'nidn' => '0001018501',
            'nip' => '198501012026011001',
            'nama_dosen' => 'Dr. Dosen Test',
            'id_prodi' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->dosenUser->update([
            'id_guru' => $this->guru->id,
            'id_dosen' => $this->dosen->id,
        ]);
    }

    public function test_guest_can_access_pmb_registration_page_and_submit()
    {
        $response = $this->get('/pmb/register');
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Mahasiswa Baru');

        $postData = [
            'nama' => 'Calon Mahasiswa Baru',
            'email' => 'pmb2026@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'id_prodi' => $this->prodi->id,
            'no_telp' => '089988776655',
            'alamat' => 'Jl. Pendidikan No. 1',
        ];

        $postResponse = $this->post('/pmb/register', $postData);
        $postResponse->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'pmb2026@test.com']);
    }

    public function test_dosen_can_view_and_approve_krs()
    {
        $krs = Krs::create([
            'id_siswa' => $this->student->id,
            'id_tahun_akademik' => $this->activeTa->id,
            'id_dosen_wali' => $this->guru->id,
            'status_krs' => 'Draft',
        ]);

        $response = $this->actingAs($this->dosenUser)->get('/siakad/dosen/krs-approval');
        $response->assertStatus(200);
        $response->assertSee('Persetujuan KRS Mahasiswa');

        $approveResponse = $this->actingAs($this->dosenUser)->post("/siakad/dosen/krs/{$krs->id}/approve", [
            'catatan_pembimbing' => 'Disetujui Dosen PA',
        ]);

        $approveResponse->assertSessionHas('success');
        $this->assertDatabaseHas('krs', [
            'id' => $krs->id,
            'status_krs' => 'Disetujui',
        ]);
    }

    public function test_dosen_can_submit_bap_meeting_log()
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF301',
            'nama_mk' => 'Pemrograman Web Enterprise',
            'sks_total' => 3,
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_dosen' => $this->dosen->id,
            'id_tahun_akademik' => $this->activeTa->id,
            'nama_kelas' => 'TI-2024-A',
            'kuota_maksimal' => 30,
        ]);

        $response = $this->actingAs($this->dosenUser)->get('/siakad/dosen/bap');
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->dosenUser)->post('/siakad/dosen/bap', [
            'id_kelas_kuliah' => $kelasKuliah->id,
            'pertemuan_ke' => 1,
            'tanggal_pelaksanaan' => '2026-10-01',
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:30',
            'materi_pembahasan' => 'Pengenalan Laravel & Enterprise Architecture',
            'catatan_dosen' => 'Mahasiswa antusias.',
            'total_mahasiswa_hadir' => 25,
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('bap_perkuliahans', [
            'id_kelas_kuliah' => $kelasKuliah->id,
            'pertemuan_ke' => 1,
        ]);
    }

    public function test_student_can_access_edom_esurat_and_facility_booking()
    {
        // EDOM Index
        $edomResponse = $this->actingAs($this->studentUser)->get('/siakad/edom');
        $edomResponse->assertStatus(200);

        // e-Surat Index & Request
        $esuratResponse = $this->actingAs($this->studentUser)->get('/siakad/esurat');
        $esuratResponse->assertStatus(200);

        $suratPost = $this->actingAs($this->studentUser)->post('/siakad/esurat', [
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'perihal' => 'Permohonan Beasiswa',
            'keperluan' => 'Persyaratan Lomba Nasional',
        ]);
        $suratPost->assertRedirect('/siakad/esurat');
        $this->assertDatabaseHas('surat_akademiks', [
            'id_siswa' => $this->student->id,
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
        ]);

        // Facility Booking Index & Request
        $facility = Facility::create([
            'nama_fasilitas' => 'Lab AI & Data Science',
            'kode_fasilitas' => 'LAB-AI-01',
            'status_fasilitas' => 'Tersedia',
        ]);

        $facilityResponse = $this->actingAs($this->studentUser)->get('/siakad/fasilitas');
        $facilityResponse->assertStatus(200);

        $facilityPost = $this->actingAs($this->studentUser)->post('/siakad/fasilitas', [
            'id_facility' => $facility->id,
            'tujuan_penggunaan' => 'Praktikum Mandiri Machine Learning',
            'tanggal_pinjam' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
        ]);
        $facilityPost->assertRedirect('/siakad/fasilitas');
        $this->assertDatabaseHas('facility_bookings', [
            'id_facility' => $facility->id,
            'id_pemohon' => $this->studentUser->id,
        ]);
    }

    public function test_baak_and_executive_dashboard_access()
    {
        $adminUser = User::create([
            'name' => 'Admin BAAK Test',
            'email' => 'adminbaak@test.ac.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'consent_pdp_at' => now(),
        ]);

        $ewsResponse = $this->actingAs($adminUser)->get('/siakad/baak/ews');
        $ewsResponse->assertStatus(200);
        $ewsResponse->assertSee('Early Warning System');

        $scanResponse = $this->actingAs($adminUser)->post('/siakad/baak/ews/scan');
        $scanResponse->assertRedirect();

        $execResponse = $this->actingAs($adminUser)->get('/siakad/eksekutif/dashboard');
        $execResponse->assertStatus(200);
        $execResponse->assertSee('Executive Control Panel');
    }
}
