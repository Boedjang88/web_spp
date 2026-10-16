<?php

namespace Tests\Feature;

use App\Filament\Resources\GuruResource;
use App\Filament\Resources\KelasResource;
use App\Filament\Resources\MataKuliahResource;
use App\Filament\Resources\PembayaranResource;
use App\Filament\Resources\SiswaResource;
use App\Filament\Resources\SppResource;
use App\Filament\Resources\UserResource;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiUxDesignOverhaulTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_standardized_navigation_groups_and_global_search_across_resources(): void
    {
        $this->assertEquals('Akademik', SiswaResource::getNavigationGroup());
        $this->assertEquals('Akademik', KelasResource::getNavigationGroup());
        $this->assertEquals('Akademik', GuruResource::getNavigationGroup());
        $this->assertEquals('Akademik', MataKuliahResource::getNavigationGroup());
        $this->assertEquals('Keuangan', PembayaranResource::getNavigationGroup());
        $this->assertEquals('Keuangan', SppResource::getNavigationGroup());
        $this->assertEquals('Manajemen Pengguna', UserResource::getNavigationGroup());

        // Global search attributes verification
        $this->assertNotEmpty(SiswaResource::getGloballySearchableAttributes());
        $this->assertContains('nisn', SiswaResource::getGloballySearchableAttributes());
        $this->assertContains('nama', SiswaResource::getGloballySearchableAttributes());

        $this->assertNotEmpty(GuruResource::getGloballySearchableAttributes());
        $this->assertContains('nip', GuruResource::getGloballySearchableAttributes());

        $this->assertNotEmpty(PembayaranResource::getGloballySearchableAttributes());
        $this->assertContains('siswa.nisn', PembayaranResource::getGloballySearchableAttributes());
    }

    /** @test */
    public function it_renders_custom_global_search_result_titles_and_details(): void
    {
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 3500000]);
        $kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak']);
        $siswa = Siswa::create([
            'nisn' => '2026001099',
            'nis' => 'MHS-099',
            'nama' => 'Budi Pratama',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Boulevard No. 1',
        ]);

        $title = SiswaResource::getGlobalSearchResultTitle($siswa);
        $this->assertStringContainsString('Budi Pratama', $title);
        $this->assertStringContainsString('2026001099', $title);

        $details = SiswaResource::getGlobalSearchResultDetails($siswa);
        $this->assertEquals('IF-3A', $details['Kelas']);

        $guru = Guru::create([
            'nip' => '198501012010121001',
            'nama_guru' => 'Dr. Budi Santoso',
            'jenis_kelamin' => 'L',
            'no_telp' => '0811111111',
            'email' => 'budi@kampus.ac.id',
            'alamat' => 'Kampus Barat',
        ]);
        $guruTitle = GuruResource::getGlobalSearchResultTitle($guru);
        $this->assertStringContainsString('Dr. Budi Santoso', $guruTitle);
        $this->assertStringContainsString('198501012010121001', $guruTitle);

        $sppTitle = SppResource::getGlobalSearchResultTitle($spp);
        $this->assertStringContainsString('2026', $sppTitle);
        $this->assertStringContainsString('3.500.000', $sppTitle);
    }

    /** @test */
    public function it_renders_filament_theme_styles_view_successfully(): void
    {
        $view = view('filament.theme-styles')->render();
        $this->assertStringContainsString('Plus Jakarta Sans', $view);
        $this->assertStringContainsString('focus-visible', $view);
        $this->assertStringContainsString('rounded-xl', $view);
    }

    /** @test */
    public function it_has_pemasukan_vs_tunggakan_chart_with_dual_datasets(): void
    {
        $widget = new \App\Filament\Widgets\PemasukanBulananChart();
        $method = new \ReflectionMethod($widget, 'getData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        $this->assertCount(2, $data['datasets']);
        $this->assertStringContainsString('Pemasukan Terverifikasi', $data['datasets'][0]['label']);
        $this->assertStringContainsString('Estimasi Tunggakan', $data['datasets'][1]['label']);
        $this->assertCount(12, $data['labels']);
    }

    /** @test */
    public function it_renders_quick_actions_widget_view_with_operational_shortcuts(): void
    {
        \Livewire\Livewire::test(\App\Filament\Widgets\QuickActionsWidget::class)
            ->assertSee('Aksi Cepat Operasional')
            ->assertSee('Akademik')
            ->assertSee('Keuangan')
            ->assertSee('Pengaturan')
            ->assertSee('Tambah Mahasiswa')
            ->assertSee('Catat Pembayaran');
    }

    /** @test */
    public function it_renders_all_enhanced_web_module_views_with_standardized_ui_tokens(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 3500000]);
        $kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $siswa = Siswa::create([
            'nisn' => '2026002001',
            'nis' => 'MHS-2001',
            'nama' => 'Rina Wijaya',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'no_telp' => '081234567891',
            'alamat' => 'Kampus 1',
        ]);
        $guru = Guru::create([
            'nip' => '198801012015011001',
            'nama_guru' => 'Budi Santoso, M.Kom',
            'jenis_kelamin' => 'L',
            'no_telp' => '0812345678',
            'email' => 'budi@test.com',
            'alamat' => 'Kampus 1',
        ]);
        $mapel = Mapel::create([
            'kode_mapel' => 'IF101',
            'nama_mapel' => 'Algoritma & Pemrograman',
            'kkm' => 75,
        ]);
        $jadwal = \App\Models\JadwalPelajaran::create([
            'id_kelas' => $kelas->id,
            'id_mapel' => $mapel->id,
            'id_guru' => $guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'ruangan' => 'Lab 1',
        ]);
        $pembayaran = \App\Models\Pembayaran::create([
            'id_petugas' => $admin->id,
            'id_siswa' => $siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2026',
            'id_spp' => $spp->id,
            'jumlah_bayar' => 3500000,
        ]);
        $nilai = \App\Models\Nilai::create([
            'id_siswa' => $siswa->id,
            'id_mapel' => $mapel->id,
            'id_guru' => $guru->id,
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'nilai_tugas' => 85,
            'nilai_uts' => 80,
            'nilai_uas' => 90,
            'nilai_akhir' => 85.5,
            'predikat' => 'A',
            'catatan' => 'Sangat Baik',
        ]);

        // 1. Jadwal Edit
        $this->actingAs($admin)->get(route('web.jadwal.edit', $jadwal->id))
            ->assertOk()
            ->assertSee('Edit Jadwal Perkuliahan')
            ->assertSee('Perbarui Jadwal');

        // 2. Nilai Create & Edit
        $this->actingAs($admin)->get(route('web.nilai.create'))
            ->assertOk()
            ->assertSee('Input Nilai Akademik')
            ->assertSee('Bobot: 30% Tugas');

        $this->actingAs($admin)->get(route('web.nilai.edit', $nilai->id))
            ->assertOk()
            ->assertSee('Edit Nilai Akademik')
            ->assertSee('Perbarui Nilai');

        // 3. Users Create & Edit
        $this->actingAs($admin)->get(route('web.users.create'))
            ->assertOk()
            ->assertSee('Tambah Pengguna Baru')
            ->assertSee('Tingkatan Hak Akses (Role)');

        $this->actingAs($admin)->get(route('web.users.edit', $admin->id))
            ->assertOk()
            ->assertSee('Edit Akun Pengguna')
            ->assertSee('Simpan Perubahan');

        // 4. Pembayaran Create & Show
        $this->actingAs($admin)->get(route('web.pembayaran.create'))
            ->assertOk()
            ->assertSee('Entri Pembayaran SPP / UKT')
            ->assertSee('Total Estimasi Pembayaran');

        $this->actingAs($admin)->get(route('web.pembayaran.show', $pembayaran->id))
            ->assertOk()
            ->assertSee('Bukti Transaksi Pembayaran')
            ->assertSee('Kwitansi Resmi SPP / UKT');

        // 5. Laporan Index
        $this->actingAs($admin)->get(route('web.laporan.index'))
            ->assertOk()
            ->assertSee('Rekapitulasi &amp; Laporan Keuangan UKT', false)
            ->assertSee('Total Pemasukan (Sesuai Filter)');
    }
}

