@extends('layouts.app')

@section('title', 'Kuliah Kerja Nyata (KKN) - Portal Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-teal-950 p-6 rounded-2xl border border-emerald-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">MODUL MAHASISWA</span>
                <span class="text-xs text-emerald-200/80">Pengabdian Masyarakat &amp; KKN Tematik</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Kuliah Kerja Nyata (KKN)</h1>
            <p class="text-xs text-emerald-200/80 mt-1">Pendaftaran lokasi, plotting kelompok, logbook kegiatan, dan pelaporan program KKN.</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Registration Form -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Form Pendaftaran KKN</h2>

            <form method="POST" action="{{ route('siakad.kkn.register') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-emerald-100 mb-1 font-mono">Pilihan Lokasi Desa/Kecamatan KKN</label>
                    <input type="text" name="lokasi_kkn" value="{{ old('lokasi_kkn', $kknRegistration?->lokasi_kkn ?? 'Desa Sukamaju, Kec. Ciawi, Kab. Bogor') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-emerald-100 mb-1 font-mono">Nama Kelompok / Posko KKN</label>
                    <input type="text" name="kelompok" value="{{ old('kelompok', $kknRegistration?->kelompok ?? 'Kelompok KKN-05') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-emerald-100 mb-1 font-mono">Dosen Pembimbing Lapangan (DPL)</label>
                    <input type="text" name="dpl_name" value="{{ old('dpl_name', $kknRegistration?->dpl_name ?? 'Dr. Ahmad Fauzi, M.T.') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan &amp; Daftar KKN</span>
                </button>
            </form>
        </div>

        <!-- Registration Status -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Status &amp; Informasi KKN Anda</h2>

            @if($kknRegistration)
                <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 font-mono">STATUS PERSYARATAN:</span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-emerald-600 text-white">TERDAFTAR &amp; AKTIF</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 text-[11px] block">Lokasi KKN:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $kknRegistration->lokasi_kkn }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Kelompok / Posko:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $kknRegistration->kelompok }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Dosen Pembimbing Lapangan:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $kknRegistration->dpl_name ?? 'DPL KKN' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Tanggal Pendaftaran:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $kknRegistration->created_at ? $kknRegistration->created_at->format('d F Y') : now()->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 text-center text-slate-400 font-mono border border-dashed rounded-xl">
                    Anda belum mendaftar kelompok KKN. Silakan isi form pendaftaran di sebelah kiri.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
