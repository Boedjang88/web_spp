@extends('layouts.app')

@section('title', 'Tugas & LMS Perkuliahan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-2xl border border-indigo-900/40 text-white shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Assignment &amp; Task Management</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Tugas &amp; Penugasan LMS</h1>
            <p class="text-sm text-slate-400 mt-0.5">Pantau tenggat waktu, unduh lembar kerja, dan kumpulkan tugas perkuliahan secara aman.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/60 text-right">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Tugas Menunggu</div>
                <div class="text-xl font-bold text-indigo-400">{{ $pendingAssignments->count() }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Task List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isSubmitted = !is_null($submission);
                $isGraded = $isSubmitted && !is_null($submission->nilai);
                $isOverdue = !$isSubmitted && $assignment->effective_deadline && $assignment->effective_deadline->isPast();
            @endphp
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm text-slate-200 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-semibold uppercase tracking-wider">
                            {{ $assignment->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }}
                        </span>
                        @if($isGraded)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">
                                Nilai: {{ $submission->nilai }}
                            </span>
                        @elseif($isSubmitted)
                            <span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-semibold">
                                Sudah Dikumpulkan
                            </span>
                        @elseif($isOverdue)
                            <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                Lewat Tenggat
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-semibold">
                                Belum Selesai
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-white mb-1 leading-snug">{{ $assignment->judul }}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">{{ $assignment->deskripsi ?? 'Tidak ada deskripsi instruksi tugas.' }}</p>
                </div>

                <div class="pt-4 border-t border-slate-800/80 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tenggat:</span>
                        </span>
                        <span class="font-medium text-slate-300">
                            {{ $assignment->effective_deadline ? $assignment->effective_deadline->format('d M Y, H:i') : 'Tanpa Batas' }}
                        </span>
                    </div>

                    <a href="{{ route('siakad.tugas.show', $assignment->id) }}" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-2 transition">
                        <span>{{ $isSubmitted ? 'Lihat Detail & Nilai' : 'Kumpulkan Tugas' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-400">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div class="text-sm font-semibold text-white">Tidak Ada Tugas Aktif</div>
                <p class="text-xs text-slate-500 mt-1">Belum ada tugas perkuliahan yang ditugaskan oleh dosen untuk kelas Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
