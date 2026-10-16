@extends('layouts.app')

@section('title', 'Edit Nilai Akademik Mahasiswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.nilai.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Nilai Akademik</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Edit Nilai</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Nilai Akademik</h1>
            <p class="text-xs text-slate-500">Perbarui komponen nilai evaluasi atau catatan capaian kompetensi</p>
        </div>
        <a href="{{ route('web.nilai.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <div class="mb-5 p-4 bg-gradient-to-r from-blue-50/60 to-indigo-50/40 border border-blue-100 rounded-2xl flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center shrink-0 shadow-sm text-sm">
                {{ strtoupper(substr($nilai->siswa?->nama ?? 'M', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900 text-sm truncate">{{ $nilai->siswa?->nama }}</span>
                    <span class="font-mono text-[11px] text-blue-600 bg-blue-100/60 px-2 py-0.5 rounded-md font-semibold">NIM/NISN: {{ $nilai->siswa?->nisn }}</span>
                </div>
                <div class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span>Mata Kuliah: <strong class="text-slate-800">{{ $nilai->mapel?->nama_mapel }}</strong></span>
                    <span class="text-slate-300">&bull;</span>
                    <span>Semester: <strong class="text-slate-800">{{ $nilai->semester }} ({{ $nilai->tahun_ajaran }})</strong></span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('web.nilai.update', $nilai->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dosen Pengampu</label>
                <select name="id_guru" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Pilih Dosen Pengampu (Opsional) --</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id }}" {{ old('id_guru', $nilai->id_guru) == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Komponen Nilai -->
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 text-xs">Komponen Nilai Evaluasi</span>
                    <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">Bobot: 30% Tugas &bull; 30% UTS &bull; 40% UAS</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai Tugas (30%)</label>
                        <input type="number" step="0.01" name="nilai_tugas" value="{{ old('nilai_tugas', (float) $nilai->nilai_tugas) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UTS (30%)</label>
                        <input type="number" step="0.01" name="nilai_uts" value="{{ old('nilai_uts', (float) $nilai->nilai_uts) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UAS (40%)</label>
                        <input type="number" step="0.01" name="nilai_uas" value="{{ old('nilai_uas', (float) $nilai->nilai_uas) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Dosen / Capaian Kompetensi</label>
                <textarea name="catatan" rows="2" placeholder="Catatan perkembangan atau evaluasi..."
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('catatan', $nilai->catatan) }}</textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.nilai.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Perbarui Nilai</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
