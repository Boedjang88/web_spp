@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div>
        <a href="{{ route('web.mapel.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Data Mapel</a>
        <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Mata Pelajaran</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.mapel.update', $mapel->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Mapel <span class="text-rose-500">*</span></label>
                <input type="text" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase font-mono">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelompok Mapel <span class="text-rose-500">*</span></label>
                    <select name="kelompok" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Kejuruan" {{ old('kelompok', $mapel->kelompok) == 'Kejuruan' ? 'selected' : '' }}>Kejuruan / Produktif</option>
                        <option value="Umum" {{ old('kelompok', $mapel->kelompok) == 'Umum' ? 'selected' : '' }}>Muatan Nasional / Umum</option>
                        <option value="Muatan Lokal" {{ old('kelompok', $mapel->kelompok) == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nilai KKM Minimum <span class="text-rose-500">*</span></label>
                    <input type="number" name="kkm" value="{{ old('kkm', $mapel->kkm) }}" required min="0" max="100"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.mapel.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md transition">Perbarui Mapel</button>
            </div>
        </form>
    </div>
</div>
@endsection
