@extends('layouts.app')

@section('title', 'Executive Dashboard & Predictive AI Analytics')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">REKTORAT &amp; EKSEKUTIF</span>
                <span class="text-xs text-blue-200/80">Real-Time Revenue &amp; GIS Demografi</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Executive Control Panel &amp; Predictive Analytics</h1>
            <p class="text-xs text-blue-200/80 mt-1">Pantauan Indikator Kinerja Utama (IKU), rasio dosen-mahasiswa, arus kas, dan prediksi kelulusan berbasis AI.</p>
        </div>
    </div>

    <!-- Executive KPI Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase font-mono tracking-wider block">Total Mahasiswa Aktif</span>
            <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ number_format($totalMahasiswa) }}</span>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase font-mono tracking-wider block">Total Dosen Pengampu</span>
            <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ number_format($totalDosen) }}</span>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase font-mono tracking-wider block">Rasio Dosen : Mahasiswa</span>
            <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">1 : {{ $ratioDosenMhs }}</span>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase font-mono tracking-wider block">Arus Kas Masuk (Real-Time)</span>
            <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 font-mono mt-1 block">Rp {{ number_format($totalPenerimaanKas, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- AI Predictive Analytics & GIS Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- AI Predictive Analytics -->
        <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Predictive AI: Tingkat Kelulusan</h2>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300">Model v2.4</span>
            </div>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Prediksi Lulus Tepat Waktu (&le; 4 Tahun)</span>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">{{ $predictiveLulusTepatWaktu }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $predictiveLulusTepatWaktu }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Potensi Keterlambatan Studi (Risk Delay)</span>
                        <span class="font-mono text-amber-600 dark:text-amber-400 font-bold">{{ $predictiveRiskDelay }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $predictiveRiskDelay }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GIS Demografi Mahasiswa -->
        <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">GIS Sebaran Demografi Mahasiswa</h2>
            <div class="space-y-3">
                @foreach($demografiKota as $kota => $persen)
                    <div>
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-slate-700 dark:text-slate-300">{{ $kota }}</span>
                            <span class="font-mono text-slate-500 dark:text-slate-400">{{ $persen }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
