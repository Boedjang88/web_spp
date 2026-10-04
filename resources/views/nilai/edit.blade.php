@extends('layouts.app')

@section('title', 'Edit Nilai Siswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div>
        <a href="{{ route('web.nilai.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Data Nilai</a>
        <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Nilai Akademik</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl text-xs">
            <span class="font-bold text-blue-900 block">{{ $nilai->siswa?->nama }} (NISN: {{ $nilai->siswa?->nisn }})</span>
            <span class="text-blue-700">Mata Pelajaran: <strong>{{ $nilai->mapel?->nama_mapel }}</strong> &bull; Semester: <strong>{{ $nilai->semester }} ({{ $nilai->tahun_ajaran }})</strong></span>
        </div>

        <form method="POST" action="{{ route('web.nilai.update', $nilai->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Guru Pengampu</label>
                <select name="id_guru" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Guru (Opsional) --</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id }}" {{ old('id_guru', $nilai->id_guru) == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Komponen Nilai -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                <span class="font-bold text-slate-700 block">Komponen Nilai:</span>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai Tugas (30%)</label>
                        <input type="number" step="0.01" name="nilai_tugas" value="{{ old('nilai_tugas', (float) $nilai->nilai_tugas) }}" required min="0" max="100"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UTS (30%)</label>
                        <input type="number" step="0.01" name="nilai_uts" value="{{ old('nilai_uts', (float) $nilai->nilai_uts) }}" required min="0" max="100"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nilai UAS (40%)</label>
                        <input type="number" step="0.01" name="nilai_uas" value="{{ old('nilai_uas', (float) $nilai->nilai_uas) }}" required min="0" max="100"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono font-bold">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Guru / Capaian Kompetensi</label>
                <textarea name="catatan" rows="2"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan', $nilai->catatan) }}</textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.nilai.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md transition">Perbarui Nilai</button>
            </div>
        </form>
    </div>
</div>
@endsection
