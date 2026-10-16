<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Mahasiswa;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class FixEmptyDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Memperbaiki seluruh field kosong pada data dummy...');

        $cities = ['Bandung', 'Jakarta Selatan', 'Surabaya', 'Semarang', 'Yogyakarta', 'Bogor', 'Medan', 'Malang', 'Cimahi', 'Bekasi'];
        $mothers = ['Siti Aminah', 'Nurhayati', 'Dewi Sartika', 'Rina Wati', 'Kartini Rahayu', 'Sri Mulyani', 'Endang Lestari', 'Yuliana Putri', 'Ratna Juwita', 'Ani Suryani'];
        $fathers = ['Bambang Sutrisno', 'Agus Setiawan', 'Hendra Wijaya', 'Budi Hartono', 'Eko Prasetyo', 'Dedi Supardi', 'Rahmat Hidayat', 'Ahmad Dahlan', 'Gunawan Wibowo', 'Iwan Falsafah'];
        $jobs = ['Wiraswasta', 'Pegawai Negeri Sipil (PNS)', 'Karyawan Swasta BUMN', 'Dosen Perguruan Tinggi', 'Profesional IT & Konsultan', 'Arsitek & Kontraktor', 'Manajer Operasional'];
        $motherJobs = ['Ibu Rumah Tangga', 'Guru Sekolah', 'Wiraswasta Mandiri', 'Pegawai Negeri Sipil', 'Staf Akuntansi & Keuangan', 'Apoteker / Medis'];
        $schools = ['SMAN 3 Bandung', 'SMAN 1 Cimahi', 'SMKN 1 Bandung', 'SMA Taruna Nusantara', 'SMAN 8 Jakarta', 'SMAN 1 Surabaya', 'SMAN 5 Semarang', 'SMKN 4 Bandung'];
        $incomes = [5500000, 7500000, 10000000, 12500000, 15000000, 18000000, 25000000];

        // 1. LENGKAPI SELURUH DATA SISWA (Jangan ada yang NULL)
        $allSiswas = Siswa::all();
        foreach ($allSiswas as $idx => $s) {
            $mName = $mothers[$s->id % count($mothers)];
            $nikGen = '327301' . sprintf('%010d', 1000000000 + $s->id * 7391);
            $waliHp = '0812' . sprintf('%08d', 70000000 + $s->id * 1234);

            $updateData = [];
            if (empty($s->nik)) {
                $updateData['nik'] = $nikGen;
            }
            if (empty($s->nama_ibu_kandung)) {
                $updateData['nama_ibu_kandung'] = $mName;
            }
            if (empty($s->no_hp_wali)) {
                $updateData['no_hp_wali'] = $waliHp;
            }
            if (empty($s->consent_pdp_at)) {
                $updateData['consent_pdp_at'] = now()->subDays(rand(10, 180));
            }
            if (empty($s->consent_pdp_ip)) {
                $updateData['consent_pdp_ip'] = '114.122.' . ($s->id % 200) . '.' . (($s->id * 7) % 250 + 1);
            }
            if (empty($s->total_skpi_points)) {
                $updateData['total_skpi_points'] = rand(35, 95);
            }
            if (empty($s->status_kelulusan)) {
                $updateData['status_kelulusan'] = 'Aktif';
            }
            if (!empty($updateData)) {
                $s->update($updateData);
            }
        }

        // 2. LENGKAPI SELURUH DATA MAHASISWA
        $allMhs = Mahasiswa::all();
        foreach ($allMhs as $idx => $m) {
            $cCity = $cities[$m->id % count($cities)];
            $mName = $mothers[$m->id % count($mothers)];
            $fName = $fathers[$m->id % count($fathers)];
            $fJob = $jobs[$m->id % count($jobs)];
            $mJob = $motherJobs[$m->id % count($motherJobs)];
            $sch = $schools[$m->id % count($schools)];
            $inc = $incomes[$m->id % count($incomes)];
            $nikGen = '327301' . sprintf('%010d', 1000000000 + $m->id * 7391);
            $waliHp = '0812' . sprintf('%08d', 70000000 + $m->id * 1234);

            $mUpdate = [];
            if (empty($m->nik)) {
                $mUpdate['nik'] = $nikGen;
            }
            if (empty($m->tempat_lahir)) {
                $mUpdate['tempat_lahir'] = $cCity;
            }
            if (empty($m->tanggal_lahir)) {
                $mUpdate['tanggal_lahir'] = '2004-' . sprintf('%02d', ($m->id % 12) + 1) . '-' . sprintf('%02d', ($m->id % 28) + 1);
            }
            if (empty($m->agama)) {
                $mUpdate['agama'] = 'Islam';
            }
            if (empty($m->alamat)) {
                $mUpdate['alamat'] = 'Jl. Telekomunikasi Terpadu No. ' . ($m->id * 2 + 1) . ', ' . $cCity;
            }
            if (empty($m->rt)) {
                $mUpdate['rt'] = sprintf('%02d', ($m->id % 10) + 1);
            }
            if (empty($m->rw)) {
                $mUpdate['rw'] = sprintf('%02d', (($m->id * 2) % 10) + 1);
            }
            if (empty($m->kelurahan)) {
                $mUpdate['kelurahan'] = 'Sukapura';
            }
            if (empty($m->kecamatan)) {
                $mUpdate['kecamatan'] = 'Dayeuhkolot';
            }
            if (empty($m->kota)) {
                $mUpdate['kota'] = $cCity;
            }
            if (empty($m->kode_pos)) {
                $mUpdate['kode_pos'] = '40257';
            }
            if (empty($m->email_pribadi)) {
                $mUpdate['email_pribadi'] = strtolower(str_replace(' ', '.', $m->nama)) . $m->id . '@gmail.com';
            }
            if (empty($m->nama_ibu_kandung)) {
                $mUpdate['nama_ibu_kandung'] = $mName;
            }
            if (empty($m->nama_ayah)) {
                $mUpdate['nama_ayah'] = 'Bpk. ' . $fName;
            }
            if (empty($m->pekerjaan_ayah)) {
                $mUpdate['pekerjaan_ayah'] = $fJob;
            }
            if (empty($m->pekerjaan_ibu)) {
                $mUpdate['pekerjaan_ibu'] = $mJob;
            }
            if (empty($m->penghasilan_ortu)) {
                $mUpdate['penghasilan_ortu'] = $inc;
            }
            if (empty($m->no_hp_wali)) {
                $mUpdate['no_hp_wali'] = $waliHp;
            }
            if (empty($m->asal_sekolah)) {
                $mUpdate['asal_sekolah'] = $sch;
            }
            if (empty($m->tahun_lulus_sekolah)) {
                $mUpdate['tahun_lulus_sekolah'] = 2023;
            }
            if (empty($m->nomor_ijazah_sekolah)) {
                $mUpdate['nomor_ijazah_sekolah'] = 'DN-02/SMA/2023/' . sprintf('%06d', 100000 + $m->id * 137);
            }
            if (empty($m->total_skpi_points)) {
                $mUpdate['total_skpi_points'] = rand(35, 95);
            }
            if (empty($m->status_kelulusan)) {
                $mUpdate['status_kelulusan'] = 'Aktif';
            }
            if (empty($m->consent_pdp_at)) {
                $mUpdate['consent_pdp_at'] = now()->subDays(rand(10, 180));
            }
            if (empty($m->consent_pdp_ip)) {
                $mUpdate['consent_pdp_ip'] = '114.122.' . ($m->id % 200) . '.' . (($m->id * 7) % 250 + 1);
            }
            if (!empty($mUpdate)) {
                $m->update($mUpdate);
            }
        }

        // 3. LENGKAPI SELURUH DATA GURU
        $allGurus = Guru::all();
        foreach ($allGurus as $g) {
            $gUpdate = [];
            if (empty($g->nik)) {
                $gUpdate['nik'] = '327301' . sprintf('%010d', 1980000000 + $g->id * 1357);
            }
            if (empty($g->no_telp)) {
                $gUpdate['no_telp'] = '0812' . sprintf('%08d', 90000000 + $g->id * 1234);
            }
            if (empty($g->email)) {
                $gUpdate['email'] = strtolower(str_replace([' ', '.', ','], '', $g->nama_guru)) . '@univ.ac.id';
            }
            if (empty($g->alamat)) {
                $gUpdate['alamat'] = 'Jl. Dago Asri No. ' . ($g->id * 5 + 10) . ', Coblong, Bandung';
            }
            if (empty($g->consent_pdp_at)) {
                $gUpdate['consent_pdp_at'] = now()->subMonths(3);
            }
            if (empty($g->consent_pdp_ip)) {
                $gUpdate['consent_pdp_ip'] = '127.0.0.1';
            }
            if (!empty($gUpdate)) {
                $g->update($gUpdate);
            }
        }

        // 4. LENGKAPI DATA DOSEN
        $allDosens = Dosen::all();
        foreach ($allDosens as $d) {
            $dUpdate = [];
            if (empty($d->nip)) {
                $dUpdate['nip'] = '198' . sprintf('%015d', 501012010011000 + $d->id);
            }
            if (empty($d->nidk)) {
                $dUpdate['nidk'] = 'NIDK-' . sprintf('%08d', 40000000 + $d->id * 321);
            }
            if (!empty($dUpdate)) {
                $d->update($dUpdate);
            }
        }

        // 5. LENGKAPI SELURUH CATATAN NILAI (Hilangkan NULL pada catatan)
        $notesByGrade = [
            'A' => [
                'Sangat Memuaskan - Penguasaan konsep teori dan implementasi praktikum luar biasa.',
                'Istimewa - Mampu menyelesaikan tugas analitis dan studi kasus dengan sangat komprehensif.',
                'Sangat Baik - Aktif berkontribusi dalam diskusi dan presentasi kelompok dengan argumentasi kuat.',
            ],
            'B' => [
                'Baik - Menguasai materi pembelajaran dengan baik dan disiplin mengumpulkan tugas.',
                'Baik - Pemahaman konsep solid, tingkatkan ketelitian pada dokumentasi kode program.',
                'Cukup Baik - Hasil ujian teori memuaskan, perkuat eksplorasi pada praktikum mandiri.',
            ],
            'C' => [
                'Cukup - Memenuhi standar kompetensi minimal, disarankan memperbanyak latihan studi kasus.',
                'Cukup - Capaian tugas sesuai standar, perlu peningkatan fokus pada evaluasi tengah semester.',
            ],
            'D' => [
                'Perlu Peningkatan - Diwajibkan mengikuti program asistensi dan remedial modul praktikum.',
            ],
        ];

        $nilaisNullCatatan = Nilai::whereNull('catatan')->orWhere('catatan', '')->get();
        foreach ($nilaisNullCatatan as $n) {
            $pred = $n->predikat ?: 'B';
            $pool = $notesByGrade[$pred] ?? $notesByGrade['B'];
            $selectedNote = $pool[$n->id % count($pool)];
            $n->update(['catatan' => $selectedNote]);
        }

        // 6. LENGKAPI DATA USERS (Verifikasi Email, Consent PDP)
        User::whereNull('email_verified_at')->update(['email_verified_at' => now()->subMonths(1)]);
        User::whereNull('consent_pdp_at')->update(['consent_pdp_at' => now()->subMonths(1), 'consent_pdp_ip' => '127.0.0.1']);

        // 7. PASTIKAN SELURUH HARI SENIN S/D SABTU MEMILIKI JADWAL
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $firstClass = \App\Models\Kelas::first();
        $firstGuru = Guru::first();
        $firstMapel = Mapel::first();

        foreach ($days as $day) {
            $count = JadwalPelajaran::where('hari', $day)->count();
            if ($count === 0 && $firstClass && $firstGuru && $firstMapel) {
                JadwalPelajaran::create([
                    'id_kelas' => $firstClass->id,
                    'id_mapel' => $firstMapel->id,
                    'id_guru' => $firstGuru->id,
                    'hari' => $day,
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:00:00',
                    'ruangan' => 'Ruang Kuliah Teori FTI 301',
                ]);
            }
        }

        $this->command?->info('Semua field kosong berhasil diperbaiki dan dilengkapi 100%!');
    }
}
