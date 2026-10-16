@extends('layouts.app')

@section('title', 'Dashboard Guru Pengajar')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Guru (Google Academic Workspace Clean Surface) -->
    <div class="bg-white dark:bg-[#1e1f20] rounded-2xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition-colors">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2 font-sans">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/60">
                    <svg class="w-3.5 h-3.5 inline-block mr-1 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    NIP: {{ $guru->nip ?? '-' }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $hariIni }}, {{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Selamat Bertugas, {{ $guru?->nama_guru ?? auth()->user()?->name ?? 'Guru' }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xl leading-relaxed">
                Kelola jadwal mengajar harian, lakukan pencatatan kehadiran presensi siswa, dan kelola nilai mata pelajaran.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('web.presensi.index') }}" class="flex-1 sm:flex-none justify-center bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2 rounded-xl text-xs shadow-xs transition inline-flex items-center gap-1.5 min-h-[38px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 02 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Presensi Kelas</span>
            </a>
            <a href="{{ route('web.nilai.create') }}" class="flex-1 sm:flex-none justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2 rounded-xl text-xs transition inline-flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 min-h-[38px]">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input Nilai Siswa</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (3 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card :padding="true">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jadwal Mengajar Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $jadwalGuruHariIni->count() }} Sesi Mengajar</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Jadwal tatap muka hari ini</p>
        </x-card>

        <x-card :padding="true">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mata Pelajaran Diampu</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $totalJadwalAjar }} Mapel / Kelas</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Alokasi beban mengajar aktif</p>
        </x-card>

        <x-card :padding="true">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Penilaian Siswa</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $nilaiTerbaruGuru->count() }} Terdaftar</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Nilai evaluasi terdata dalam sistem</p>
        </x-card>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Jadwal Mengajar & Presensi (7 Cols) -->
        <div class="lg:col-span-7">
            <x-card title="Jadwal Mengajar Hari Ini ({{ $hariIni }})" subtitle="Jam pelajaran tatap muka dan ruang kelas" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.jadwal.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline">
                        Semua Jadwal &rarr;
                    </a>
                </x-slot:action>

                <div class="p-5 space-y-3">
                    @forelse($jadwalGuruHariIni as $j)
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50 hover:border-brand-500 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start sm:items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center text-sm shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $j->mapel->nama_mapel ?? '-' }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                        Kelas <span class="font-bold text-brand-600 dark:text-brand-400">{{ $j->kelas->nama_kelas ?? '-' }}</span> &bull; Ruang <span class="font-bold text-slate-700 dark:text-slate-300">{{ $j->ruangan ?? 'Kelas' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="self-start sm:self-auto flex sm:flex-col items-center sm:items-end gap-2 shrink-0">
                                <span class="px-3 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono font-bold text-brand-600 dark:text-brand-400 shadow-2xs">
                                    {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                </span>
                                <a href="{{ route('web.presensi.index') }}" class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    Presensi
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-mono">
                            Tidak ada jadwal mengajar pada hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Recent Grades Queue (5 Cols) -->
        <div class="lg:col-span-5">
            <x-card title="Entri Nilai Siswa Terakhir" subtitle="Riwayat penilaian tugas dan ujian terkini" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.nilai.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline">
                        Semua Nilai &rarr;
                    </a>
                </x-slot:action>

                <div class="p-5 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($nilaiTerbaruGuru as $n)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $n->siswa?->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                    {{ $n->mapel?->nama_mapel ?? '-' }} &bull; Kelas {{ $n->siswa?->kelas?->nama_kelas ?? '-' }}
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
                            Belum ada entri nilai siswa terbaru.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

</div>
@endsection
