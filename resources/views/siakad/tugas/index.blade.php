@extends('layouts.app')

@section('title', 'Tugas & LMS Perkuliahan')

@section('content')
<div class="space-y-5">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-zinc-900 dark:bg-zinc-950 p-5 md:p-6 rounded-xl border border-zinc-800 text-white">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700 text-[10px] font-mono font-semibold mb-2">
                <span>ASSIGNMENT &amp; LMS PORTAL</span>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">Tugas &amp; Penugasan LMS</h1>
            <p class="text-xs text-zinc-400 mt-1 max-w-2xl leading-relaxed">Pantau tenggat waktu, unduh berkas instruksi, dan kumpulkan tugas perkuliahan secara aman.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-3.5 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-right font-mono">
                <div class="text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">Tugas Menunggu</div>
                <div class="text-lg font-bold text-white">{{ $pendingAssignments->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Task List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isSubmitted = !is_null($submission);
                $isGraded = $isSubmitted && !is_null($submission->nilai);
                $isOverdue = !$isSubmitted && $assignment->effective_deadline && $assignment->effective_deadline->isPast();
            @endphp
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 flex flex-col justify-between hover:border-zinc-400 dark:hover:border-zinc-600 transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-semibold uppercase tracking-wider">
                            {{ $assignment->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }}
                        </span>
                        @if($isGraded)
                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono font-bold">
                                Nilai: {{ $submission->nilai }}
                            </span>
                        @elseif($isSubmitted)
                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono font-bold">
                                Dikumpulkan
                            </span>
                        @elseif($isOverdue)
                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono font-bold">
                                Lewat Tenggat
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-mono font-bold">
                                Menunggu
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1 leading-snug">{{ $assignment->judul }}</h3>
                    <p class="text-xs text-zinc-500 line-clamp-2 mb-3 leading-relaxed">{{ $assignment->deskripsi ?? 'Tidak ada deskripsi instruksi tugas.' }}</p>
                </div>

                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 space-y-2.5">
                    <div class="flex items-center justify-between text-[11px] text-zinc-500 font-mono">
                        <span>Tenggat:</span>
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $assignment->effective_deadline ? $assignment->effective_deadline->format('d M Y, H:i') : 'Tanpa Batas' }}
                        </span>
                    </div>

                    <a href="{{ route('siakad.tugas.show', $assignment->id) }}" class="w-full py-2 px-3 rounded-lg bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span>{{ $isSubmitted ? 'Lihat Detail & Nilai' : 'Kumpulkan Tugas' }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-10 text-center rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-500">
                <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Tidak Ada Tugas Aktif</div>
                <p class="text-xs text-zinc-400 mt-1">Belum ada tugas perkuliahan yang ditugaskan oleh dosen untuk kelas Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
