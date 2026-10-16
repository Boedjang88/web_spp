@extends('layouts.app')

@section('title', 'Portal Mandiri Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Mahasiswa (Google Academic Workspace Clean Surface) -->
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition-colors">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2 font-sans">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/60">
                    <svg class="w-3.5 h-3.5 inline-block mr-1 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    Portal Mahasiswa &bull; {{ $siswa?->nim ? 'NIM' : 'NIM / NISN' }}: {{ $siswa?->nim ?? $siswa?->nisn ?? $siswa?->nis ?? '-' }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                    Status: {{ $siswa?->status_kelulusan ?? 'Aktif' }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $hariIni }}, {{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Selamat Datang, {{ $siswa?->nama ?? auth()->user()?->name ?? 'Mahasiswa' }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xl leading-relaxed">
                Kelola rencana studi Smart KRS, presensi kuliah harian, kumpulkan tugas LMS, dan status pembayaran UKT.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('siakad.krs.index') }}" class="flex-1 sm:flex-none justify-center bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2 rounded-xl text-xs shadow-xs transition inline-flex items-center gap-1.5 min-h-[38px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Smart KRS</span>
            </a>
            <a href="{{ route('siakad.presensi.index') }}" class="flex-1 sm:flex-none justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2 rounded-xl text-xs transition inline-flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 min-h-[38px]">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Presensi</span>
            </a>
            <a href="{{ route('siakad.ukt.index') }}" class="flex-1 sm:flex-none justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2 rounded-xl text-xs transition inline-flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 min-h-[38px]">
                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>UKT</span>
            </a>
        </div>
    </div>

    <!-- Quick Metrics Summary Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status SPP / UKT -->
        <x-card :padding="true">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Status Keuangan UKT</span>
            @if(($tunggakan['total_bulan'] ?? 0) === 0)
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">LUNAS</div>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">Tidak ada tunggakan berjalan</p>
            @else
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono">Rp {{ number_format($tunggakan['total_rupiah'] ?? 0, 0, ',', '.') }}</div>
                <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium mt-1">{{ $tunggakan['total_bulan'] }} periode belum lunas</p>
            @endif
        </x-card>

        <!-- Presensi -->
        <x-card :padding="true">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Tingkat Kehadiran</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $presensiSummary['persentase'] ?? 100 }}%</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">{{ $presensiSummary['hadir'] ?? 0 }} Hadir &bull; {{ ($presensiSummary['izin'] ?? 0) + ($presensiSummary['sakit'] ?? 0) }} Izin/Sakit &bull; {{ $presensiSummary['alpa'] ?? 0 }} Alpa</p>
        </x-card>

        <!-- Jadwal Hari Ini -->
        <x-card :padding="true">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Sesi Kuliah ({{ $hariIni }})</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $jadwalSiswa->count() }} Sesi</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Jadwal tatap muka hari ini</p>
        </x-card>

        <!-- Status Rencana Studi -->
        <x-card :padding="true">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">Status Rencana Studi</span>
            <div class="text-2xl font-black text-brand-600 dark:text-brand-400 font-mono">Disetujui</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Kelas: {{ $siswa?->kelas?->nama_kelas ?? 'Reguler' }}</p>
        </x-card>
    </div>

    <!-- Main Content Grid: Above the Fold Priority -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Jadwal Perkuliahan Hari Ini (7 Cols) -->
        <div class="lg:col-span-7">
            <x-card title="Jadwal Perkuliahan Hari Ini ({{ $hariIni }})" subtitle="Agenda kuliah tatap muka dan ruangan" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.jadwal.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline">
                        Lihat Semua Jadwal &rarr;
                    </a>
                </x-slot:action>

                <div class="p-5 space-y-3">
                    @forelse($jadwalSiswa as $j)
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50 hover:border-brand-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start sm:items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center text-sm shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $j->mapel->nama_mapel ?? '-' }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                        Dosen: <strong class="text-slate-700 dark:text-slate-300">{{ $j->guru->nama_guru ?? '-' }}</strong> &bull; Ruang: <strong class="text-slate-700 dark:text-slate-300">{{ $j->ruangan ?? 'Ruang Teori' }}</strong>
                                    </p>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono font-bold text-brand-600 dark:text-brand-400 shadow-2xs shrink-0">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-mono">
                            Tidak ada agenda perkuliahan untuk hari ini.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right: Ringkasan Nilai & Transkrip (5 Cols) -->
        <div class="lg:col-span-5">
            <x-card title="Transkrip Nilai Akademik" subtitle="Hasil evaluasi semester aktif" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.nilai.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline">
                        Lihat KHS &rarr;
                    </a>
                </x-slot:action>

                <div class="p-5 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($nilaiSiswa as $n)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $n->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Tugas: {{ (float) $n->nilai_tugas }} &bull; UTS: {{ (float) $n->nilai_uts }} &bull; UAS: {{ (float) $n->nilai_uas }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800">
                                    {{ (float) $n->nilai_akhir }} ({{ $n->predikat }})
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-mono">
                            Belum ada data nilai akademik yang diumumkan.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

</div>
@endsection
