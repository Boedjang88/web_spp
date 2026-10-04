<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\BapPerkuliahan;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Gedung;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Ruangan;
use App\Models\Submission;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\Ukt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StudentPortalAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected Mahasiswa $mahasiswa;
    protected User $studentUser;
    protected Mahasiswa $otherMahasiswa;
    protected User $otherStudentUser;
    protected Dosen $dosen;
    protected User $dosenUser;
    protected TahunAkademik $tahunAkademik;
    protected KelasKuliah $kelasKuliah;
    protected Ruangan $ruangan;
    protected Assignment $assignment;
    protected TagihanUkt $tagihanUkt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // Master Academic
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

        $kurikulum = Kurikulum::create([
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_mulai' => 2026,
            'total_sks_lulus' => 144,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => 'Ganjil 2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'tgl_mulai' => '2026-09-01',
            'tgl_selesai' => '2027-01-31',
        ]);

        $ukt = Ukt::create([
            'id_prodi' => $prodi->id,
            'tahun' => 2026,
            'kelompok_ukt' => 'UKT 3',
            'nominal' => 3500000.00,
            'biaya_praktikum' => 250000.00,
            'biaya_kemahasiswaan' => 50000.00,
        ]);

        $this->dosen = Dosen::create([
            'nidn' => '0412058509',
            'nip' => '198505122010121009',
            'nama_dosen' => 'Dr. Budi Santoso, M.Kom.',
            'id_prodi' => $prodi->id,
            'is_active' => true,
        ]);

        $this->dosenUser = User::create([
            'name' => 'Dr. Budi Santoso, M.Kom.',
            'email' => 'budi.dosen@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'dosen',
            'id_dosen' => $this->dosen->id,
            'consent_pdp_at' => now(),
        ]);

        $gedung = Gedung::create(['kode_gedung' => 'G-A', 'nama_gedung' => 'Gedung A']);
        $this->ruangan = Ruangan::create([
            'id_gedung' => $gedung->id,
            'kode_ruangan' => 'R-301',
            'nama_ruangan' => 'Ruang Teori 301',
            'kapasitas' => 40,
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'radius_meter' => 20,
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $kurikulum->id,
            'kode_mk' => 'IF101',
            'nama_mk' => 'Pemrograman Web Enterprise',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $this->kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_dosen' => $this->dosen->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF101-A',
            'ruang' => 'R-301',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        // Student 1
        $this->mahasiswa = Mahasiswa::create([
            'nim' => '2301010001',
            'nik' => '3201123456780001',
            'nama' => 'Bintang Mahasiswa',
            'id_prodi' => $prodi->id,
            'id_dosen_pa' => $this->dosen->id,
            'id_ukt' => $ukt->id,
            'alamat' => 'Jl. Dipatiukur No. 1',
            'nama_ibu_kandung' => 'Dewi Lestari',
            'no_telp' => '081223344556',
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $guru = \App\Models\Guru::create([
            'nip' => '198505122010121009',
            'nama_guru' => 'Dr. Budi Santoso, M.Kom.',
            'jenis_kelamin' => 'L',
            'no_telp' => '081234567890',
        ]);

        $spp = \App\Models\Spp::create(['tahun' => 2026, 'nominal' => 3800000]);
        $kelas = \App\Models\Kelas::create(['nama_kelas' => 'TI-1A', 'kompetensi_keahlian' => 'Teknik Informatika']);

        $siswa = \App\Models\Siswa::create([
            'nisn' => '0054321901',
            'nis' => '2301010001',
            'nik' => '3201123456780001',
            'nama' => 'Bintang Mahasiswa',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Dipatiukur No. 1',
            'no_telp' => '081223344556',
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $this->studentUser = User::create([
            'name' => 'Bintang Mahasiswa',
            'email' => 'bintang@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'mahasiswa',
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_siswa' => $siswa->id,
            'consent_pdp_at' => now(),
        ]);

        // Student 2 (Other Student)
        $this->otherMahasiswa = Mahasiswa::create([
            'nim' => '2301010002',
            'nik' => '3201123456780002',
            'nama' => 'Citra Mahasiswa',
            'id_prodi' => $prodi->id,
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $otherSiswa = \App\Models\Siswa::create([
            'nisn' => '0054321902',
            'nis' => '2301010002',
            'nik' => '3201123456780002',
            'nama' => 'Citra Mahasiswa',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Merdeka No. 2',
            'no_telp' => '081234567899',
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $this->otherStudentUser = User::create([
            'name' => 'Citra Mahasiswa',
            'email' => 'citra@univ.ac.id',
            'password' => bcrypt('secret123'),
            'role' => 'mahasiswa',
            'id_mahasiswa' => $this->otherMahasiswa->id,
            'id_siswa' => $otherSiswa->id,
            'consent_pdp_at' => now(),
        ]);

        // KRS for Student 1
        $krs = Krs::create([
            'id_siswa' => $siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'id_dosen_wali' => $guru->id,
            'total_sks_diambil' => 3,
            'status_krs' => 'Disetujui',
        ]);

        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $this->kelasKuliah->id,
            'status_ambil' => 'Baru',
        ]);

        // Assignment
        $this->assignment = Assignment::create([
            'id_kelas_kuliah' => $this->kelasKuliah->id,
            'judul' => 'Tugas 1 Arsitektur Microservices',
            'deskripsi' => 'Rancang arsitektur API Gateway dengan Laravel Sanctum',
            'bobot_persen' => 15.00,
            'bobot_nilai_bap' => 15.00,
            'deadline_at' => now()->addDays(3),
            'is_published' => true,
        ]);

        // Tagihan UKT for Student 1
        $this->tagihanUkt = TagihanUkt::create([
            'id_mahasiswa' => $this->mahasiswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nomor_va' => '9882301010001001',
            'nomor_invoice' => 'INV-20261-0001',
            'biaya_ukt' => 3500000.00,
            'biaya_praktikum' => 250000.00,
            'biaya_kemahasiswaan' => 50000.00,
            'total_tagihan' => 3800000.00,
            'total_harus_bayar' => 3800000.00,
            'total_sudah_bayar' => 0.00,
            'status_pembayaran' => 'Belum Bayar',
            'tgl_jatuh_tempo' => now()->addMonths(1),
        ]);
    }

    /**
     * Test 1: Student can access Presensi portal and submit attendance check-in within 20m tolerance
     */
    public function test_student_can_view_presensi_and_check_in(): void
    {
        $bap = BapPerkuliahan::create([
            'id_kelas_kuliah' => $this->kelasKuliah->id,
            'id_guru' => $this->dosen->id,
            'id_ruangan' => $this->ruangan->id,
            'pertemuan_ke' => 1,
            'tanggal_pelaksanaan' => now()->toDateString(),
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:30',
            'materi_pembahasan' => 'Overview Laravel Architecture',
            'status_verifikasi' => 'Diverifikasi BAAK',
        ]);

        // 1. Student views attendance page
        $response = $this->actingAs($this->studentUser)->get(route('siakad.presensi.index'));
        $response->assertStatus(200);
        $response->assertSee('Presensi Perkuliahan');
        $response->assertSee('Overview Laravel Architecture');

        // 2. Set token in cache
        $token = 'ATT-QR-TEST12345';
        Cache::put("qr_attendance_bap_{$bap->id}", $token, now()->addSeconds(10));

        // 3. Submit check-in within 20m
        $checkInResponse = $this->actingAs($this->studentUser)
            ->post(route('siakad.presensi.checkIn'), [
                'id_bap' => $bap->id,
                'qr_token' => $token,
                'lat' => -6.917465,
                'lng' => 107.619124,
            ]);

        $checkInResponse->assertRedirect();
        $checkInResponse->assertSessionHas('success');

        $this->assertDatabaseHas('presensi_mahasiswas', [
            'id_mahasiswa' => $this->mahasiswa->id,
            'status' => 'Hadir',
        ]);
    }

    /**
     * Test 2: Student can view tasks and submit assignment with file upload & hash receipt
     */
    public function test_student_can_view_assignments_and_submit_solution(): void
    {
        // 1. View task list
        $response = $this->actingAs($this->studentUser)->get(route('siakad.tugas.index'));
        $response->assertStatus(200);
        $response->assertSee('Tugas 1 Arsitektur Microservices');

        // 2. View task detail
        $detailResponse = $this->actingAs($this->studentUser)->get(route('siakad.tugas.show', $this->assignment->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Rancang arsitektur API Gateway dengan Laravel Sanctum');

        // 3. Submit assignment with valid PDF
        $file = UploadedFile::fake()->createWithContent('laporan_microservices.pdf', "%PDF-1.7\nSample content\n%%EOF");

        $submitResponse = $this->actingAs($this->studentUser)
            ->post(route('siakad.tugas.submit', $this->assignment->id), [
                'file_tugas' => $file,
                'catatan' => 'Laporan microservices lengkap dengan benchmark.',
            ]);

        $submitResponse->assertRedirect();
        $submitResponse->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', [
            'id_assignment' => $this->assignment->id,
            'id_mahasiswa' => $this->mahasiswa->id,
            'original_filename' => 'laporan_microservices.pdf',
        ]);
    }

    /**
     * Test 3: Student can view UKT bill, pay via simulation, and print receipt
     */
    public function test_student_can_pay_ukt_bill_and_print_receipt(): void
    {
        // 1. View UKT Portal
        $response = $this->actingAs($this->studentUser)->get(route('siakad.ukt.index'));
        $response->assertStatus(200);
        $response->assertSee('9882301010001001'); // VA Number
        $response->assertSee('3.800.000');

        // 2. Pay UKT Bill via simulation
        $payResponse = $this->actingAs($this->studentUser)
            ->post(route('siakad.ukt.bayar', $this->tagihanUkt->id), [
                'channel' => 'BNI Virtual Account',
            ]);

        $payResponse->assertRedirect();
        $payResponse->assertSessionHas('success');

        $this->tagihanUkt->refresh();
        $this->assertEquals('Lunas', $this->tagihanUkt->status_pembayaran);

        // 3. Access printable receipt
        $pembayaran = \App\Models\PembayaranUkt::where('id_mahasiswa', $this->mahasiswa->id)->first();
        $this->assertNotNull($pembayaran);

        $receiptResponse = $this->actingAs($this->studentUser)->get(route('siakad.ukt.kwitansi', $pembayaran->id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($pembayaran->nomor_kuitansi);

        // 4. Other student cannot access Student 1's receipt (IDOR protection)
        $idorResponse = $this->actingAs($this->otherStudentUser)->get(route('siakad.ukt.kwitansi', $pembayaran->id));
        $idorResponse->assertStatus(403);
    }

    /**
     * Test 4: Student can edit and update full biodata with encrypted PDP fields
     */
    public function test_student_can_complete_biodata_with_encrypted_pdp_data(): void
    {
        // 1. View biodata form
        $response = $this->actingAs($this->studentUser)->get(route('siakad.biodata.edit'));
        $response->assertStatus(200);
        $response->assertSee('Lengkapi Biodata Mahasiswa');

        // 2. Update biodata
        $updateResponse = $this->actingAs($this->studentUser)->put(route('siakad.biodata.update'), [
            'nama' => 'Bintang Mahasiswa Putra',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2004-05-15',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'nik' => '3201123456789999',
            'no_telp' => '081299887766',
            'email_pribadi' => 'bintang.private@gmail.com',
            'alamat' => 'Jl. Cisitu Indah No. 45',
            'rt' => '002',
            'rw' => '007',
            'kelurahan' => 'Dago',
            'kecamatan' => 'Coblong',
            'kota' => 'Kota Bandung',
            'kode_pos' => '40135',
            'nama_ayah' => 'Hendro Santoso',
            'nama_ibu_kandung' => 'Ratna Juwita',
            'pekerjaan_ayah' => 'PNS',
            'pekerjaan_ibu' => 'Guru',
            'penghasilan_ortu' => '5.000.000 - 10.000.000',
            'no_hp_wali' => '081344556677',
            'asal_sekolah' => 'SMAN 3 Bandung',
            'tahun_lulus_sekolah' => '2023',
            'nomor_ijazah_sekolah' => 'DN-02/MA/2023/123456',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->mahasiswa->refresh();
        $this->assertEquals('Bintang Mahasiswa Putra', $this->mahasiswa->nama);
        $this->assertEquals('Bandung', $this->mahasiswa->tempat_lahir);
        $this->assertEquals('Kota Bandung', $this->mahasiswa->kota);
        $this->assertEquals('SMAN 3 Bandung', $this->mahasiswa->asal_sekolah);
        $this->assertEquals('Ratna Juwita', $this->mahasiswa->nama_ibu_kandung); // Decrypted via cast
        $this->assertEquals('3201123456789999', $this->mahasiswa->nik); // Decrypted via cast
    }
}
