@extends('layouts.app')

@section('title', 'Portal Mandiri Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Mahasiswa (Deep Ocean Navy & Royal Blue) -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-blue-900/50">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30 font-mono">
                    <svg class="w-4 h-4 inline-block text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg> Portal Mahasiswa &bull; NIM: {{ $siswa->nisn ?? $siswa->nim ?? '-' }}
                </span>
                <span class="text-xs text-blue-200/80 font-medium">Status: {{ $siswa->status_kelulusan ?? 'Aktif' }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Selamat Datang, {{ $siswa->nama ?? auth()->user()->name }}!</h1>
            <p class="text-blue-200/80 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Kelola rencana studi Smart KRS, presensi GPS, pengumpulkan tugas LMS, dan billing pembayaran UKT Anda.
            </p>
        </div>
        @if($siswa)
            <div class="relative z-10 flex flex-wrap items-center gap-2">
                <a href="{{ route('siakad.krs.index') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-lg shadow-blue-600/30 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Smart KRS</span>
                </a>
                <a href="{{ route('siakad.ukt.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/20">
                    <svg class="w-4 h-4 inline-block text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Billing UKT</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Shortcuts Grid for Portal Features -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('siakad.presensi.index') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-blue-500 transition">Presensi GPS</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Check-in Perkuliahan</span>
        </a>

        <a href="{{ route('siakad.tugas.index') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-blue-500 transition">Tugas &amp; LMS</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Kumpulkan Tugas</span>
        </a>

        <a href="{{ route('siakad.ukt.index') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-emerald-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 transition">Pembayaran UKT</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Cetak Kwitansi H2H</span>
        </a>

        <a href="{{ route('siakad.krs.index') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-blue-500 transition">Smart KRS</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Rencana Studi</span>
        </a>

        <a href="{{ route('siakad.biodata.edit') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-amber-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-amber-500 transition">Biodata &amp; PDP</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Data Diri &amp; Consent</span>
        </a>

        <a href="{{ route('siakad.analytics.performance') }}" class="p-4 rounded-2xl bg-white dark:bg-[#1c2541] border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-purple-500 transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-purple-500 transition">Grafik Performa</span>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Tren IPK &amp; IPS</span>
        </a>
    </div>

    <!-- Quick Metrics Summary Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status SPP / UKT -->
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300 block mb-1">Status Keuangan UKT</span>
            @if($tunggakan['total_bulan'] === 0)
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">LUNAS</div>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">Tidak ada tunggakan berjalan</p>
            @else
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }}</div>
                <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium mt-1">{{ $tunggakan['total_bulan'] }} periode belum lunas</p>
            @endif
        </div>

        <!-- Presensi -->
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300 block mb-1">Tingkat Kehadiran</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $presensiSummary['persentase'] }}%</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">{{ $presensiSummary['hadir'] }} Hadir &bull; {{ $presensiSummary['izin'] + $presensiSummary['sakit'] }} Izin/Sakit &bull; {{ $presensiSummary['alpa'] }} Alpa</p>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300 block mb-1">Sesi Kuliah ({{ $hariIni }})</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $jadwalSiswa->count() }} Sesi</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Jadwal 2-4 sesi per hari</p>
        </div>

        <!-- SKS Terambil -->
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300 block mb-1">Status Rencana Studi</span>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400">Disetujui</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Semester 2025/2026 Ganjil</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Jadwal Perkuliahan Hari Ini (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">Jadwal Perkuliahan Hari Ini ({{ $hariIni }})</h2>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($jadwalSiswa as $j)
                    <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-700/60 bg-slate-50/60 dark:bg-[#0b132b] hover:border-blue-500 transition flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-600 dark:text-blue-400 font-bold flex items-center justify-center text-sm shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Dosen: <strong class="text-slate-700 dark:text-slate-300">{{ $j->guru->nama_guru ?? '-' }}</strong> &bull; Ruang: <strong class="text-slate-700 dark:text-slate-300">{{ $j->ruangan ?? 'Ruang Teori' }}</strong></div>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-mono font-bold text-blue-600 dark:text-blue-400 shadow-2xs shrink-0">
                            {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Tidak ada agenda perkuliahan untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Ringkasan Nilai & Transkrip (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">Transkrip Nilai Akademik</h2>
                </div>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($nilaiSiswa as $n)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $n->mapel->nama_mapel ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Tugas: {{ $n->nilai_tugas }} &bull; UTS: {{ $n->nilai_uts }} &bull; UAS: {{ $n->nilai_uas }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                {{ $n->nilai_akhir }} ({{ $n->predikat }})
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Belum ada data nilai akademik.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
