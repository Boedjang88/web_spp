@extends('layouts.app')

@section('title', 'Pendaftaran Wisuda & Clearance Kelulusan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-purple-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/20 text-purple-200 border border-purple-400/30">MODUL YUDISIUM &amp; WISUDA</span>
                <span class="text-xs text-purple-200/80">Clearance Kelulusan &amp; Penomoran Ijazah Nasional (PIN)</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Wisudawan &amp; Clearance Yudisium</h1>
            <p class="text-xs text-purple-200/80 mt-1">Verifikasi bebas pustaka, bebas SPP/UKT, bebas laboratorium, dan pendaftaran wisuda resmi.</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Status Clearance -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Hasil Verifikasi System Clearance</h2>

            <div class="space-y-3 text-xs">
                <div class="p-4 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200 block">1. Clearance Keuangan &amp; UKT</span>
                        <span class="text-[11px] text-slate-500">Status bebas tunggakan biaya perkuliahan &amp; SPP.</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full font-bold font-mono text-[10px] bg-emerald-100 text-emerald-700 border border-emerald-300">LUNAS &amp; OK</span>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200 block">2. Clearance Perpustakaan Utama</span>
                        <span class="text-[11px] text-slate-500">Status penyerahan karya ilmiah &amp; bebas pinjam buku.</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full font-bold font-mono text-[10px] bg-emerald-100 text-emerald-700 border border-emerald-300">BEBAS PUSTAKA</span>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200 block">3. Clearance SKS &amp; Beban Studi</span>
                        <span class="text-[11px] text-slate-500">Penyelesaian SKS minimal &amp; verifikasi nilai tanpa E/F.</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full font-bold font-mono text-[10px] bg-emerald-100 text-emerald-700 border border-emerald-300">MEMENUHI SYARAT</span>
                </div>
            </div>
        </div>

        <!-- Registration Action -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Pendaftaran Wisuda Periode 2026</h2>

            @if($siswa?->nomor_ijazah)
                <div class="p-4 bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 rounded-xl space-y-2 text-xs">
                    <span class="font-bold text-purple-900 dark:text-purple-300 block text-sm">PENDAFTARAN WISUDA BERHASIL!</span>
                    <div class="text-slate-600 dark:text-slate-300">
                        Penomoran Ijazah Nasional (PIN):
                        <span class="font-mono font-bold text-purple-700 dark:text-purple-400 block text-sm mt-0.5">{{ $siswa->nomor_ijazah }}</span>
                    </div>
                    <span class="text-[11px] text-slate-400 block">Tanggal Kelulusan Resmi: {{ $siswa->tgl_kelulusan ? $siswa->tgl_kelulusan->format('d F Y') : now()->format('d F Y') }}</span>
                </div>
            @else
                <form method="POST" action="{{ route('siakad.wisuda.register') }}" class="space-y-4 text-xs">
                    @csrf
                    <p class="text-slate-600 dark:text-slate-400">Seluruh syarat clearance yudisium Anda telah terpenuhi. Klik tombol di bawah ini untuk mengajukan wisuda &amp; memicu pembuatan PIN Ijazah Nasional.</p>
                    <button type="submit" class="w-full py-3 px-4 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-md shadow-purple-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>Daftar Wisuda &amp; Terbitkan PIN</span>
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>
@endsection
