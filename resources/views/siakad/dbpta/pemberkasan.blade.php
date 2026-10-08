@extends('layouts.app')

@section('title', 'DBPTA - Pemberkasan Final Skripsi')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">MODUL DBPTA</span>
                <span class="text-xs text-blue-200/80">Unggah Dokumen Softcopy Final &amp; Bebas Pustaka</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Pemberkasan Final Skripsi / TA</h1>
            <p class="text-xs text-blue-200/80 mt-1">Pengunggahan berkas revisi pasca sidang, lembar pengesahan bertanda tangan, dan publikasi repositori.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Upload -->
        <div class="lg:col-span-6 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Formulir Unggah Berkas Final</h2>

            <form method="POST" action="{{ route('siakad.dbpta.pemberkasan.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Link Repositori / Google Drive Berkas Final (PDF) <span class="text-rose-500">*</span></label>
                    <input type="url" name="link_berkas" value="{{ old('link_berkas', $tugasAkhir?->file_revisi_path) }}" required placeholder="https://drive.google.com/file/d/..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-xs">
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Simpan &amp; Serahkan Berkas Final</span>
                </button>
            </form>
        </div>

        <!-- Checklist -->
        <div class="lg:col-span-6 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Kelengkapan Berkas Yudisium</h2>

            <div class="space-y-3 text-xs">
                <div class="p-3.5 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <span>1. Lembar Pengesahan Tanda Tangan Pembimbing &amp; Penguji</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">LENGKAP</span>
                </div>
                <div class="p-3.5 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <span>2. Jurnal / Artikel Ilmiah Softcopy</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">TERUNGGAH</span>
                </div>
                <div class="p-3.5 bg-slate-50 dark:bg-[#0b132b] rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <span>3. Bebas Pustaka Perpustakaan Kampus</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">VERIFIKASI OK</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
