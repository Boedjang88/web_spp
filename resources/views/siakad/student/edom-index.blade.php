@extends('layouts.app')

@section('title', 'Evaluasi Dosen oleh Mahasiswa (EDOM)')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">EVALUASI DOSEN (EDOM)</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Evaluasi Dosen Oleh Mahasiswa</h1>
            <p class="text-xs text-blue-200/80 mt-1">Berikan penilaian objektif atas pengajaran dosen untuk meningkatkan mutu akademik perkuliahan.</p>
        </div>
    </div>

    <!-- EDOM Courses List & Questionnaire Forms -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-6">
        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Daftar Mata Kuliah &amp; Evaluasi Dosen</h2>

        @if($krs && $krs->details->count() > 0)
            <div class="space-y-6">
                @foreach($krs->details as $detail)
                    @php
                        $kk = $detail->kelasKuliah;
                        $isEvaluated = in_array($detail->id, $existingEdoms);
                    @endphp
                    <div class="p-5 rounded-2xl border {{ $isEvaluated ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-[#0b132b]' }} transition space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ $kk?->mataKuliah?->kode_mk }} &bull; {{ $kk?->nama_kelas }}</span>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base mt-0.5">{{ $kk?->mataKuliah?->nama_mk }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Dosen Pengampu: <strong class="text-slate-800 dark:text-slate-200">{{ $kk?->dosen?->nama_dosen ?? $kk?->dosen?->nama ?? 'Dosen Pengampu' }}</strong></p>
                            </div>
                            <div>
                                @if($isEvaluated)
                                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-full text-xs font-mono font-bold">
                                        &check; Evaluasi Selesai
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/40 rounded-full text-xs font-mono font-bold">
                                        Belum Dievaluasi
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if(!$isEvaluated)
                            <form method="POST" action="{{ route('siakad.edom.store') }}" class="space-y-4 pt-2 text-xs">
                                @csrf
                                <input type="hidden" name="id_krs_detail" value="{{ $detail->id }}">
                                <input type="hidden" name="id_guru" value="{{ $kk?->id_dosen ?? 1 }}">

                                <div class="space-y-3">
                                    @foreach($pertanyaans as $pIdx => $p)
                                        <div class="p-3 bg-white dark:bg-[#1c2541] border border-slate-200 dark:border-slate-800 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                            <div class="flex-1">
                                                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-500 font-mono text-[10px] font-bold rounded uppercase mr-2">{{ $p->kategori }}</span>
                                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $p->teks_pertanyaan }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 font-mono">
                                                <span class="text-[10px] text-slate-400">1 (Kurang)</span>
                                                @for($skor = 1; $skor <= 5; $skor++)
                                                    <label class="inline-flex items-center gap-1 cursor-pointer">
                                                        <input type="radio" name="skor[{{ $p->id }}]" value="{{ $skor }}" {{ $skor == 5 ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $skor }}</span>
                                                    </label>
                                                @endfor
                                                <span class="text-[10px] text-slate-400">5 (Sangat Baik)</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Kritik &amp; Saran Konstruktif untuk Dosen</label>
                                    <textarea name="kritik_saran" rows="2" placeholder="Tuliskan masukan untuk peningkatan kualitas pengajaran..."
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl text-xs"></textarea>
                                </div>

                                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Kirim Evaluasi Dosen</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center text-slate-400 font-mono">
                Belum ada rencana studi (KRS) aktif untuk semester ini.
            </div>
        @endif
    </div>

</div>
@endsection
