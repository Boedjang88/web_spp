@extends('layouts.app')

@section('title', 'Input Nilai Akademik Mahasiswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.nilai.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Nilai Akademik</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Input Nilai</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Input Nilai Akademik</h1>
            <p class="text-xs text-slate-500">Kalkulasi dan simpan nilai formatif, UTS, serta UAS mahasiswa</p>
        </div>
        <a href="{{ route('web.nilai.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.nilai.store') }}" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Mahasiswa <span class="text-rose-500">*</span></label>
                <select name="id_siswa" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" {{ (old('id_siswa', $selectedSiswaId) == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} (NIM: {{ $s->nim ?? $s->nisn }}) - Kelas {{ $s->kelas?->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Kuliah <span class="text-rose-500">*</span></label>
                    <select name="id_mapel" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ old('id_mapel') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }} (Batas KKM: {{ $m->kkm }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dosen Pengampu</label>
                    <select name="id_guru" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Dosen Pengampu (Opsional) --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}" {{ old('id_guru') == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                    <select name="semester" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', '2025/2026') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>
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
                        <input type="number" step="0.01" name="nilai_tugas" value="{{ old('nilai_tugas', 85) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UTS (30%)</label>
                        <input type="number" step="0.01" name="nilai_uts" value="{{ old('nilai_uts', 80) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UAS (40%)</label>
                        <input type="number" step="0.01" name="nilai_uas" value="{{ old('nilai_uas', 88) }}" required min="0" max="100"
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold text-slate-800 transition">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Dosen / Capaian Kompetensi</label>
                <textarea name="catatan" rows="2" placeholder="Catatan perkembangan belajar atau capaian kompetensi..."
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('catatan') }}</textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.nilai.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Kalkulasi &amp; Simpan Nilai</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
