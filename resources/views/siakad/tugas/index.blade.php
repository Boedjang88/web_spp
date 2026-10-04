@extends('layouts.app')

@section('title', 'Tugas & LMS Perkuliahan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-brand-900 p-6 sm:p-8 rounded-2xl border border-slate-800 text-white shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-brand-200 border border-white/10 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Assignment &amp; Task Management</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Tugas &amp; Penugasan LMS</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">Pantau tenggat waktu, unduh berkas instruksi, dan kumpulkan tugas perkuliahan secara aman.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right">
                <div class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Tugas Menunggu</div>
                <div class="text-xl font-black text-brand-300">{{ $pendingAssignments->count() }}</div>
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
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 flex flex-col justify-between hover:border-brand-300 transition group">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 text-[10px] font-bold uppercase tracking-wider">
                            {{ $assignment->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }}
                        </span>
                        @if($isGraded)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                Nilai: {{ $submission->nilai }}
                            </span>
                        @elseif($isSubmitted)
                            <span class="px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 border border-brand-200 text-[10px] font-bold">
                                Dikumpulkan
                            </span>
                        @elseif($isOverdue)
                            <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">
                                Lewat Tenggat
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">
                                Menunggu
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5 leading-snug group-hover:text-brand-600 transition">{{ $assignment->judul }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">{{ $assignment->deskripsi ?? 'Tidak ada deskripsi instruksi tugas.' }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tenggat:</span>
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $assignment->effective_deadline ? $assignment->effective_deadline->format('d M Y, H:i') : 'Tanpa Batas' }}
                        </span>
                    </div>

                    <a href="{{ route('siakad.tugas.show', $assignment->id) }}" class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-semibold flex items-center justify-center gap-2 transition shadow-xs">
                        <span>{{ $isSubmitted ? 'Lihat Detail & Nilai' : 'Kumpulkan Tugas' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-500">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div class="text-sm font-bold text-slate-900">Tidak Ada Tugas Aktif</div>
                <p class="text-xs text-slate-400 mt-1">Belum ada tugas perkuliahan yang ditugaskan oleh dosen untuk kelas Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
