@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Guru (Calm & Elegant) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-emerald-950 rounded-2xl p-6 md:p-8 text-white shadow-soft relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-slate-800">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-emerald-300 border border-white/10">
                    👨‍🏫 Portal Pengajar / Dewan Guru &bull; NIP: {{ $guru->nip ?? '-' }}
                </span>
                <span class="text-xs text-slate-300">{{ $hariIni }}, {{ now()->format('d M Y') }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Selamat Bertugas, {{ $guru->nama_guru ?? auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Kelola agenda mengajar kelas hari ini, lakukan presensi kehadiran siswa, dan masukkan nilai hasil evaluasi belajar.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('web.presensi.index') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-sm transition inline-flex items-center gap-1.5">
                <span>📋</span> Presensi Kelas
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/15 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/10">
                <span>📝</span> Input Nilai
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
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">🗓️ Jadwal Mengajar Hari Ini ({{ $hariIni }})</h2>
                    <p class="text-[11px] text-slate-400">Daftar kelas dan jam mata pelajaran yang Anda ampu hari ini.</p>
                </div>
                <a href="{{ route('web.jadwal.index') }}" class="text-xs text-emerald-600 font-semibold hover:underline">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($jadwalGuruHariIni as $j)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-emerald-50/30 transition flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-700 font-bold flex items-center justify-center text-sm">
                                📖
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
                        <div class="text-2xl mb-1">🎉</div>
                        Tidak ada agenda jadwal mengajar untuk hari {{ $hariIni }}.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Input Nilai Terkini (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">📝 Penilaian Terkini</h2>
                    <p class="text-[11px] text-slate-400">Entri nilai evaluasi belajar siswa terakhir.</p>
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
