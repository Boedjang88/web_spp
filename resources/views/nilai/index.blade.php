@extends('layouts.app')

@section('title', 'Nilai Akademik Mahasiswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800">SIAKAD</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Evaluasi Akademik &amp; KHS</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Nilai Akademik &amp; E-KHS Mahasiswa</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Input nilai Tugas, UTS, UAS, kalkulasi IPS/IPK akhir otomatis, predikat, dan cetak KHS</p>
        </div>
        <a href="{{ route('web.nilai.create') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 min-h-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Nilai Mahasiswa</span>
        </a>
    </div>

    <!-- Filter Card -->
    <x-card :padding="true">
        <form method="GET" action="{{ route('web.nilai.index') }}" class="flex flex-col md:flex-row items-center gap-3 text-xs">
            <div class="w-full md:flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa atau NIM..."
                    class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">
            </div>

            <div class="w-full md:w-48">
                <select name="id_kelas" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">
                    <option value="">-- Semua Kelas Kuliah --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="id_mapel" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">
                    <option value="">-- Semua Mata Kuliah --</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id }}" {{ request('id_mapel') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-slate-900 dark:bg-brand-600 text-white rounded-xl font-semibold hover:bg-slate-800 transition min-h-9.5">Filter</button>
                @if(request('search') || request('id_kelas') || request('id_mapel'))
                    <a href="{{ route('web.nilai.index') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-semibold transition min-h-9.5 flex items-center justify-center">Reset</a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table & Card Container -->
    <x-card :padding="false">
        
        <!-- Desktop Table View (>= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">Mahasiswa &amp; Kelas</th>
                        <th class="py-3.5 px-4 font-semibold">Mata Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Tugas (30%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">UTS (30%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">UAS (40%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Predikat</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($nilais as $n)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $n->siswa?->nama }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NIM: {{ $n->siswa?->nim ?? $n->siswa?->nisn }} &bull; {{ $n->siswa?->kelas?->nama_kelas }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $n->mapel?->nama_mapel }}</div>
                                <div class="text-[10px] text-slate-400">KKM: {{ $n->mapel?->kkm }} &bull; Sem: {{ $n->semester }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_tugas }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uts }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uas }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold {{ $n->nilai_akhir >= ($n->mapel?->kkm ?? 75) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800' }}">
                                    {{ (float) $n->nilai_akhir }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $n->predikat }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('web.nilai.khs', $n->id_siswa) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-700 dark:text-brand-300 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900 border border-brand-200/80 dark:border-brand-800 px-2 py-1 rounded-lg transition" title="Cetak KHS Mahasiswa">
                                        <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        E-KHS
                                    </a>
                                    <a href="{{ route('web.nilai.edit', $n->id) }}"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 dark:text-amber-300 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-800 px-2 py-1 rounded-lg transition" title="Edit Nilai">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('web.nilai.destroy', $n->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Hapus catatan nilai {{ $n->siswa?->nama }} untuk matkul {{ $n->mapel?->nama_mapel }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 border border-rose-200/80 dark:border-rose-800 px-2 py-1 rounded-lg transition" title="Hapus Nilai">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="font-medium text-xs">Belum ada data nilai akademik yang tercatat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Card List (< 768px) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800 p-4 space-y-4">
            @forelse($nilais as $n)
                <div class="pt-4 first:pt-0 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-[10px] text-slate-400 font-mono block">NIM: {{ $n->siswa?->nim ?? $n->siswa?->nisn }} &bull; {{ $n->siswa?->kelas?->nama_kelas }}</span>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $n->siswa?->nama }}</h3>
                            <p class="text-xs font-semibold text-brand-600 dark:text-brand-400 mt-0.5 truncate">{{ $n->mapel?->nama_mapel }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold block {{ $n->nilai_akhir >= ($n->mapel?->kkm ?? 75) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800' }}">
                                {{ (float) $n->nilai_akhir }} ({{ $n->predikat }})
                            </span>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Sem {{ $n->semester }}</span>
                        </div>
                    </div>

                    <!-- Component Breakdown -->
                    <div class="grid grid-cols-3 gap-2 p-2.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl text-center font-mono text-xs border border-slate-200/60 dark:border-slate-800/80">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-sans">Tugas (30%)</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ (float) $n->nilai_tugas }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-sans">UTS (30%)</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ (float) $n->nilai_uts }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-sans">UAS (40%)</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ (float) $n->nilai_uas }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('web.nilai.khs', $n->id_siswa) }}" target="_blank"
                            class="flex-1 text-center py-2 px-3 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center">
                            Cetak KHS
                        </a>
                        <a href="{{ route('web.nilai.edit', $n->id) }}"
                            class="px-3 py-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center">
                            Edit
                        </a>
                        <form action="{{ route('web.nilai.destroy', $n->id) }}" method="POST"
                            onsubmit="return confirm('Hapus catatan nilai {{ $n->siswa?->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs font-medium">
                    Belum ada data nilai akademik yang tercatat.
                </div>
            @endforelse
        </div>

        @if($nilais->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                {{ $nilais->links() }}
            </div>
        @endif
    </x-card>

</div>
@endsection
