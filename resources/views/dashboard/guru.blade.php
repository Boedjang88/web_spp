@extends('layouts.app')

@section('title', 'Dashboard Dosen Pengajar')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Dosen (Deep Ocean Navy & Royal Blue) -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-blue-900/50">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30 font-mono">
                    <svg class="w-4 h-4 inline-block text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Portal Dosen Pengajar &bull; NIDN: {{ $guru->nidn ?? $guru->nip ?? '0415018501' }}
                </span>
                <span class="text-xs text-blue-200/80 font-medium">{{ $hariIni }}, {{ now()->format('d M Y') }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Selamat Bertugas, {{ $guru->nama_guru ?? auth()->user()->name }}!</h1>
            <p class="text-blue-200/80 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Kelola perkuliahan aktif, lakukan presensi kehadiran mahasiswa per pertemuan, serta evaluasi hasil perkuliahan.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('web.presensi.index') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-lg shadow-blue-600/30 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 02 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Kelola Presensi Perkuliahan</span>
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input Nilai Mahasiswa</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300">Sesi Mengajar Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $jadwalGuruHariIni->count() }} Sesi Kelas</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Jadwal tatap muka hari ini (2-4 sesi/hari)</p>
        </div>

        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300">Total Mata Kuliah Diampu</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $totalJadwalAjar }} Mata Kuliah</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Alokasi 2-3 mata kuliah per dosen</p>
        </div>

        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-blue-300">Status Penilaian Mahasiswa</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $nilaiTerbaruGuru->count() }} Terdaftar</div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Nilai evaluasi terdata dalam sistem</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Jadwal & Presensi Perkuliahan Hari Ini (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">Jadwal Mengajar &amp; Kontrol Presensi ({{ $hariIni }})</h2>
                </div>
                <a href="{{ route('web.jadwal.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($jadwalGuruHariIni as $j)
                    <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-700/60 bg-slate-50/60 dark:bg-[#0b132b] hover:border-blue-500 transition flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-600 dark:text-blue-400 font-bold flex items-center justify-center text-sm shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Kelas <span class="font-bold text-blue-600 dark:text-blue-400">{{ $j->kelas->nama_kelas ?? '-' }}</span> &bull; Ruang <span class="font-bold text-slate-700 dark:text-slate-300">{{ $j->ruangan ?? 'Ruang Teori' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-mono font-bold text-blue-600 dark:text-blue-400 block shadow-2xs">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                            <a href="{{ route('web.presensi.index') }}?id_kelas={{ $j->id_kelas }}" class="mt-1.5 inline-flex items-center gap-1 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold transition shadow-xs">
                                <span>Input Presensi Mahasiswa</span> &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Tidak ada agenda mengajar hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rekap Penilaian Terkini (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">Penilaian Terkini Dosen</h2>
                </div>
                <a href="{{ route('web.nilai.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">Semua Nilai &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($nilaiTerbaruGuru as $n)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $n->siswa->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $n->mapel->nama_mapel ?? '-' }} &bull; {{ $n->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono {{ $n->status === 'Lulus' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                                {{ $n->nilai_akhir }} ({{ $n->predikat }})
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Belum ada data penilaian mahasiswa.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
