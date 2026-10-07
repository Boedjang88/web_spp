@extends('layouts.app')

@section('title', 'Tugas & LMS Perkuliahan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-blue-200 border border-white/10 text-[10px] font-mono font-bold uppercase mb-2">
                <span>ASSIGNMENT &amp; LMS PORTAL</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Tugas &amp; Penugasan LMS</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">Pantau tenggat waktu, unduh berkas instruksi, dan kumpulkan tugas perkuliahan secara aman.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right font-mono">
                <div class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Tugas Menunggu</div>
                <div class="text-lg font-black text-amber-400">{{ $pendingAssignments->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Task List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isSubmitted = !is_null($submission);
                $isGraded = $isSubmitted && !is_null($submission->nilai);
                $isOverdue = !$isSubmitted && $assignment->effective_deadline && $assignment->effective_deadline->isPast();
            @endphp
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-slate-100 flex flex-col justify-between hover:border-blue-300 dark:hover:border-blue-700 shadow-soft hover:shadow-card transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-900 text-blue-700 dark:text-blue-300 text-[10px] font-mono font-bold uppercase tracking-wider">
                            {{ $assignment->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }}
                        </span>
                        @if($isGraded)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-mono font-bold">
                                Nilai: {{ $submission->nilai }}
                            </span>
                        @elseif($isSubmitted)
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-[10px] font-mono font-bold">
                                ✓ Dikumpulkan
                            </span>
                        @elseif($isOverdue)
                            <span class="px-2.5 py-1 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-[10px] font-mono font-bold">
                                ⚠ Lewat Tenggat
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-mono font-bold">
                                ⏱ Menunggu
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-1.5 leading-snug">{{ $assignment->judul }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-4 leading-relaxed">{{ $assignment->deskripsi ?? 'Tidak ada deskripsi instruksi tugas.' }}</p>
                </div>

                <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                        <span>Tenggat:</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">
                            {{ $assignment->effective_deadline ? $assignment->effective_deadline->format('d M Y, H:i') : 'Tanpa Batas' }}
                        </span>
                    </div>

                    <a href="{{ route('siakad.tugas.show', $assignment->id) }}" class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition shadow-sm">
                        <span>{{ $isSubmitted ? 'Lihat Detail & Nilai' : 'Kumpulkan Tugas' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-500 shadow-soft">
                <div class="text-sm font-bold text-slate-900 dark:text-slate-100">Tidak Ada Tugas Aktif</div>
                <p class="text-xs text-slate-400 mt-1">Belum ada tugas perkuliahan yang ditugaskan oleh dosen untuk kelas Anda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
