@extends('layouts.app')

@section('title', 'DBPTA - Pengajuan Proposal Skripsi / Tugas Akhir')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">MODUL DBPTA</span>
                <span class="text-xs text-blue-200/80">Database &amp; Pendaftaran Proposal Skripsi</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Pengajuan Proposal Tugas Akhir / Skripsi</h1>
            <p class="text-xs text-blue-200/80 mt-1">Registrasi judul, rumusan masalah, latar belakang, dan draf proposal awal ke Program Studi.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Proposal -->
        <div class="lg:col-span-6 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Formulir Proposal Skripsi</h2>

            <form method="POST" action="{{ route('siakad.dbpta.proposal.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Judul Penelitian / Skripsi <span class="text-rose-500">*</span></label>
                    <textarea name="judul" rows="3" required placeholder="Contoh: Rancang Bangun Sistem Informasi Akademik Berbasis Microservices Menggunakan Laravel..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-medium">{{ old('judul', $tugasAkhir?->judul) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Ringkasan / Abstrak Proposal <span class="text-rose-500">*</span></label>
                    <textarea name="abstrak" rows="5" required placeholder="Uraikan latar belakang masalah, tujuan penelitian, dan metodologi yang akan digunakan..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">{{ old('abstrak', $tugasAkhir?->abstrak) }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Kirim Proposal ke Kaprodi &amp; Dosen</span>
                </button>
            </form>
        </div>

        <!-- Status Proposal -->
        <div class="lg:col-span-6 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Status &amp; Tinjauan Proposal</h2>

            @if($tugasAkhir)
                <div class="p-4 bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-blue-800 dark:text-blue-300 font-mono">STATUS PROPOSAL:</span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-amber-500 text-white uppercase">{{ $tugasAkhir->status_persetujuan ?? 'PENDING' }}</span>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-slate-500 text-[11px] block font-mono">Judul Penelitian Disetujui:</span>
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $tugasAkhir->judul }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block font-mono">Tanggal Pengajuan:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $tugasAkhir->created_at ? $tugasAkhir->created_at->format('d F Y, H:i') : now()->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-400 font-mono border border-dashed rounded-xl">
                    Belum ada proposal Tugas Akhir yang diajukan. Silakan isi form di sebelah kiri.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
