<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Mahasiswa;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParentChildOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $studentId = $user?->id_mahasiswa ?? $user?->id_siswa;

        $mahasiswa = Mahasiswa::with(['prodi', 'ukt'])->find($studentId)
            ?? Siswa::with(['kelas', 'spp'])->find($studentId);

        if (!$mahasiswa) {
            return [
                Stat::make('Mahasiswa', 'Belum Terhubung')
                    ->description('Akun belum dihubungkan ke data mahasiswa')
                    ->color('danger'),
            ];
        }

        $namaMahasiswa = $mahasiswa->nama;
        $nim = $mahasiswa->nim ?? $mahasiswa->nisn ?? '-';
        $prodi = $mahasiswa->prodi?->nama_prodi ?? $mahasiswa->kelas?->nama_kelas ?? '-';

        return [
            Stat::make('Nama Mahasiswa', $namaMahasiswa)
                ->description("NIM/NISN: {$nim}")
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Program Studi', $prodi)
                ->description('Status: Mahasiswa Aktif')
                ->icon('heroicon-o-building-library')
                ->color('primary'),

            Stat::make('Portal Perwalian', 'Akses Terverifikasi')
                ->description('Hak Akses: Strictly Read-Only')
                ->icon('heroicon-o-shield-check')
                ->color('info'),
        ];
    }
}
