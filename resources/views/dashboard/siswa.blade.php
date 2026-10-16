@extends('layouts.app')

@section('title', 'Portal Mandiri Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Siswa (Calm, Non-glaring Matte Workspace Surface) -->
    <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 sm:p-6 md:p-7 border border-slate-200/80 dark:border-slate-700/60 shadow-xs transition-colors">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
            <div class="flex items-start sm:items-center gap-4">
                <!-- Student Avatar Badge -->
                <div class="relative shrink-0">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-brand-600 text-white font-bold text-xl flex items-center justify-center shadow-xs overflow-hidden">
                        {{ strtoupper(substr($siswa?->nama ?? auth()->user()?->name ?? 'S', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full" title="Status Aktif"></span>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2 font-sans">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600/60 inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            Portal Mahasiswa &bull; NISN: {{ $siswa?->nisn ?? $siswa?->nis ?? '-' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Status: {{ $siswa?->status_kelulusan ?? 'Aktif' }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $hariIni }}, {{ now()->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                        Selamat Datang, {{ $siswa?->nama ?? auth()->user()?->name ?? 'Siswa' }}!
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                        Pantau jadwal pelajaran harian, rekap kehadiran presensi, nilai rapor, dan status pembayaran SPP Anda.
                    </p>
                </div>
            </div>

            <!-- Quick Access Primary Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto shrink-0">
                <a href="{{ route('web.jadwal.index') }}" class="flex-1 sm:flex-none justify-center bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-xs hover:shadow-sm transition inline-flex items-center gap-2 min-h-9.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Jadwal Pelajaran</span>
                </a>
                <a href="{{ route('siakad.ukt.index') }}" class="flex-1 sm:flex-none justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-xs transition inline-flex items-center gap-2 border border-slate-200 dark:border-slate-600 min-h-9.5">
                    <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Status SPP</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Metrics Summary Cards (4 Columns with Soothing Palette) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status SPP -->
        <div class="bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs transition hover:border-slate-300 dark:hover:border-slate-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Keuangan SPP</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/50 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            @if(($tunggakan['total_bulan'] ?? 0) === 0)
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">LUNAS</div>
                <div class="mt-2 text-[11px] text-emerald-700 dark:text-emerald-300 font-medium flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Tidak ada tunggakan berjalan</span>
                </div>
            @else
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono tracking-tight">Rp {{ number_format($tunggakan['total_rupiah'] ?? 0, 0, ',', '.') }}</div>
                <div class="mt-2 text-[11px] text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>{{ $tunggakan['total_bulan'] }} bulan belum lunas</span>
                </div>
            @endif
        </div>

        <!-- Presensi -->
        <div class="bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs transition hover:border-slate-300 dark:hover:border-slate-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tingkat Kehadiran</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/50 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 font-mono tracking-tight">{{ $presensiSummary['persentase'] ?? 100 }}%</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>{{ $presensiSummary['hadir'] ?? 0 }} Hadir &bull; {{ ($presensiSummary['izin'] ?? 0) + ($presensiSummary['sakit'] ?? 0) }} Izin &bull; {{ $presensiSummary['alpa'] ?? 0 }} Alpa</span>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs transition hover:border-slate-300 dark:hover:border-slate-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pelajaran Hari Ini ({{ $hariIni }})</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/50 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 font-mono tracking-tight">{{ $jadwalSiswa->count() }} Sesi</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                <span>Jadwal tatap muka di kelas</span>
            </div>
        </div>

        <!-- Kelas -->
        <div class="bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-5 shadow-xs transition hover:border-slate-300 dark:hover:border-slate-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rombongan Belajar</span>
                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 border border-brand-200/60 dark:border-brand-800/50 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-brand-600 dark:text-brand-400 font-mono tracking-tight">{{ $siswa?->kelas?->nama_kelas ?? 'Kelas Reguler' }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                <span>{{ $siswa?->kelas?->kompetensi_keahlian ?? 'Jurusan Umum' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Jadwal Pelajaran Hari Ini (7 Cols) -->
        <div class="lg:col-span-7">
            <x-card title="Jadwal Pelajaran Hari Ini ({{ $hariIni }})" subtitle="Agenda jam tatap muka dan ruang kelas" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.jadwal.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline inline-flex items-center gap-1">
                        <span>Lihat Semua Jadwal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </x-slot:action>

                <div class="p-5 space-y-3">
                    @forelse($jadwalSiswa as $j)
                        <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-800/40 border-l-4 border-l-brand-500 hover:bg-slate-50 dark:hover:bg-slate-800/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/40 border border-brand-200/60 dark:border-brand-800/50 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center text-sm shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 dark:text-slate-100 text-sm truncate">{{ $j->mapel->nama_mapel ?? '-' }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        <span class="inline-flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Guru: {{ $j->guru->nama_guru ?? '-' }}
                                        </span>
                                        <span>&bull;</span>
                                        <span class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            Ruang: {{ $j->ruangan ?? 'Kelas' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-brand-700 dark:text-brand-300 shadow-2xs shrink-0 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400 dark:text-slate-500 text-xs font-mono">
                            <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Tidak ada agenda pelajaran untuk hari ini.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right: Ringkasan Nilai & Rapor (5 Cols) -->
        <div class="lg:col-span-5">
            <x-card title="Nilai Akademik &amp; Rapor" subtitle="Hasil penilaian semester berjalan" :padding="false">
                <x-slot:action>
                    <a href="{{ route('web.nilai.index') }}" class="text-xs text-brand-600 dark:text-brand-400 font-semibold hover:underline inline-flex items-center gap-1">
                        <span>Lihat Nilai</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </x-slot:action>

                <div class="p-5 divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($nilaiSiswa as $n)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ $n->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Tugas: <strong class="text-slate-700 dark:text-slate-300">{{ (float) $n->nilai_tugas }}</strong> &bull; UTS: <strong class="text-slate-700 dark:text-slate-300">{{ (float) $n->nilai_uts }}</strong> &bull; UAS: <strong class="text-slate-700 dark:text-slate-300">{{ (float) $n->nilai_uas }}</strong>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold font-mono bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                                    {{ (float) $n->nilai_akhir }} ({{ $n->predikat }})
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400 dark:text-slate-500 text-xs font-mono">
                            <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Belum ada data nilai yang diumumkan.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

</div>
@endsection
