@extends('layouts.app')

@section('title', 'Dashboard Dosen Pengajar')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Dosen (Calm & Elegant) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-emerald-950 rounded-2xl p-6 md:p-8 text-white shadow-soft relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-slate-800">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-emerald-300 border border-white/10">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Portal Dosen Pengajar &bull; NIDN: {{ $guru->nidn ?? $guru->nip ?? '-' }}
                </span>
                <span class="text-xs text-slate-300">{{ $hariIni }}, {{ now()->format('d M Y') }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Selamat Bertugas, {{ $guru->nama_guru ?? auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Kelola agenda perkuliahan hari ini, lakukan presensi kehadiran mahasiswa, dan masukkan nilai hasil evaluasi akademik.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('web.presensi.index') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-sm transition inline-flex items-center gap-1.5">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 02 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></span> Presensi Perkuliahan
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/15 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/10">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span> Input Nilai
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Jadwal Hari Ini ({{ $hariIni }})</span>
            <div class="text-2xl font-bold text-slate-900">{{ $jadwalGuruHariIni->count() }} Sesi Kelas</div>
            <p class="text-[11px] text-slate-500 mt-1">Jadwal tatap muka hari ini</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Total Alokasi Mengajar</span>
            <div class="text-2xl font-bold text-slate-900">{{ $totalJadwalAjar }} Jadwal</div>
            <p class="text-[11px] text-slate-500 mt-1">Total sesi mengajar per pekan</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Entri Nilai Masuk</span>
            <div class="text-2xl font-bold text-slate-900">{{ $nilaiTerbaruGuru->count() }} Terdata</div>
            <p class="text-[11px] text-slate-500 mt-1">Rekap nilai siswa terkini</p>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Jadwal Mengajar Hari Ini (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h2 class="font-bold text-slate-900 text-sm">Jadwal Mengajar Hari Ini ({{ $hariIni }})</h2>
                </div>
                <a href="{{ route('web.jadwal.index') }}" class="text-xs text-emerald-600 font-semibold hover:underline">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($jadwalGuruHariIni as $j)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-emerald-50/30 transition flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-700 font-bold flex items-center justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Kelas <span class="font-semibold text-slate-700">{{ $j->kelas->nama_kelas ?? '-' }}</span> &bull; Ruang <span class="font-semibold text-slate-700">{{ $j->ruangan ?? 'Kelas' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono font-semibold text-emerald-700 shadow-2xs block">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                            <a href="{{ route('web.presensi.index') }}?id_kelas={{ $j->id_kelas }}" class="text-[10px] text-emerald-600 font-bold hover:underline mt-1 inline-block">
                                Presensi &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Tidak ada agenda jadwal mengajar untuk hari {{ $hariIni }}.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Input Nilai Terkini (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <h2 class="font-bold text-slate-900 text-sm">Penilaian Terkini</h2>
                </div>
                <a href="{{ route('web.nilai.index') }}" class="text-xs text-emerald-600 font-semibold hover:underline">Semua Nilai &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($nilaiTerbaruGuru as $n)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-semibold text-slate-900">{{ $n->siswa->nama ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $n->mapel->nama_mapel ?? '-' }} &bull; {{ $n->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $n->status === 'Lulus' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $n->nilai_akhir }} ({{ $n->predikat }})
                            </span>
                            <div class="text-[9px] text-slate-400 mt-0.5">{{ $n->status }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada data penilaian siswa.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
