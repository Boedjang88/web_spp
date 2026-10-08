@extends('layouts.app')

@section('title', 'DBPTA - Sidang Yudisium & Skripsi')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-violet-950 via-slate-900 to-purple-950 p-6 rounded-2xl border border-violet-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-violet-500/20 text-violet-200 border border-violet-400/30">MODUL DBPTA</span>
                <span class="text-xs text-violet-200/80">Sidang Ujian Skripsi &amp; Verifikasi Yudisium</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Sidang Yudisium &amp; Ujian Skripsi</h1>
            <p class="text-xs text-violet-200/80 mt-1">Plotting jadwal sidang, dosen penguji, penilaian akhir, dan kelulusan yudisium.</p>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Registration -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Form Pengajuan Sidang Skripsi</h2>

            <form method="POST" action="{{ route('siakad.dbpta.sidang.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="p-4 bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-800 rounded-xl space-y-2">
                    <span class="font-bold text-violet-900 dark:text-violet-300 block">SYARAT AKADEMIK SIDANG:</span>
                    <ul class="list-disc list-inside text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                        <li>Minimal 8x logbook bimbingan disetujui Dosen PA</li>
                        <li>Sertifikat Bebas Pustaka Perpustakaan</li>
                        <li>Lunas seluruh tagihan SPP / UKT</li>
                    </ul>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-bold shadow-md shadow-violet-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Daftar Sidang Skripsi / Yudisium</span>
                </button>
            </form>
        </div>

        <!-- Schedule / Status -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Jadwal &amp; Hasil Sidang Skripsi</h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Jenis Sidang / Tanggal</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Ruangan / Nilai</th>
                            <th class="py-3 px-3 text-center">Status Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($sidangList as $idx => $sidang)
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-bold text-violet-600 dark:text-violet-400 block text-xs">{{ $sidang->jenis_sidang }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $sidang->tgl_sidang ? $sidang->tgl_sidang->format('d F Y') : '-' }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-700 dark:text-slate-300 border-r border-slate-100 dark:border-slate-800">
                                    <div class="font-semibold">{{ $sidang->ruangan }}</div>
                                    <div class="text-[10px] text-emerald-600 font-mono font-bold">Nilai Akhir: {{ $sidang->nilai_akhir ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        {{ $sidang->status_lulus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-mono">Belum ada pendaftaran sidang skripsi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
