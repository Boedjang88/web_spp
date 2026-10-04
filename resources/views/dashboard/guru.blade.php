@extends('layouts.app')

@section('title', 'Dashboard Tenaga Pendidik')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Guru -->
    <div class="bg-gradient-to-r from-emerald-700 via-teal-700 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur">
                    👨‍🏫 Portal Pengajar / Dewan Guru
                </span>
                <span class="text-xs text-emerald-200">Hari ini: {{ $hariIni }}, {{ now()->format('d M Y') }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight">Selamat Bertugas, {{ $guru->nama_guru ?? auth()->user()->name }}!</h1>
            <p class="text-emerald-100 text-xs md:text-sm mt-1 max-w-xl">
                NIP: {{ $guru->nip ?? '-' }} &bull; Kelola agenda mengajar hari ini, lakukan presensi kehadiran kelas, dan masukkan nilai hasil evaluasi belajar siswa.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2">
            <a href="{{ route('web.presensi.index') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
                <span>📋</span> Presensi Siswa
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5">
                <span>📝</span> Input Nilai
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Jadwal Hari Ini ({{ $hariIni }})</span>
            <div class="text-2xl font-black text-slate-900">{{ $jadwalGuruHariIni->count() }} Sesi Kelas</div>
            <p class="text-[11px] text-slate-500 mt-1">Jadwal tatap muka hari ini</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Alokasi Mengajar</span>
            <div class="text-2xl font-black text-slate-900">{{ $totalJadwalAjar }} Jadwal</div>
            <p class="text-[11px] text-slate-500 mt-1">Total sesi dalam satu pekan</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Entri Nilai Masuk</span>
            <div class="text-2xl font-black text-slate-900">{{ $nilaiTerbaruGuru->count() }} Terdata</div>
            <p class="text-[11px] text-slate-500 mt-1">Rekap evaluasi terkini</p>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Jadwal Mengajar Hari Ini (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">🗓️ Jadwal Mengajar Hari Ini ({{ $hariIni }})</h2>
                    <p class="text-[11px] text-slate-400">Daftar kelas dan jam mata pelajaran yang Anda ampu hari ini.</p>
                </div>
                <a href="{{ route('web.jadwal.index') }}" class="text-xs text-emerald-600 font-semibold hover:underline">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($jadwalGuruHariIni as $j)
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/30 transition flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 font-black flex items-center justify-center text-xs">
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
                            <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono font-bold text-emerald-700 shadow-sm block">
                                ⏰ {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                            <a href="{{ route('web.presensi.index') }}?id_kelas={{ $j->id_kelas }}" class="text-[10px] text-emerald-600 font-bold hover:underline mt-1 inline-block">
                                Presensi Kelas &rarr;
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
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">📝 Penilaian Terkini</h2>
                    <p class="text-[11px] text-slate-400">Entri nilai evaluasi siswa terakhir yang Anda simpan.</p>
                </div>
                <a href="{{ route('web.nilai.index') }}" class="text-xs text-emerald-600 font-semibold hover:underline">Semua Nilai &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($nilaiTerbaruGuru as $n)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $n->siswa->nama ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $n->mapel->nama_mapel ?? '-' }} &bull; {{ $n->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $n->status === 'Lulus' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
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
