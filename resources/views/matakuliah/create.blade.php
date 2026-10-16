@extends('layouts.app')

@section('title', 'Tambah Mata Kuliah Baru')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.matakuliah.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Data Mata Kuliah</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Tambah Baru</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Mata Kuliah Baru</h1>
            <p class="text-xs text-slate-500">Input kode matkul, nama mata kuliah, kelompok keahlian, dan batas KKM kelulusan</p>
        </div>
        <a href="{{ route('web.matakuliah.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.matakuliah.store') }}" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Mata Kuliah <span class="text-rose-500">*</span></label>
                <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" required placeholder="Contoh: IF-201, TIF-302"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white uppercase font-mono transition">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Mata Kuliah <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak Terdistribusi"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelompok Kurikulum / SKS <span class="text-rose-500">*</span></label>
                    <select name="kelompok" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="Kejuruan" {{ old('kelompok') == 'Kejuruan' ? 'selected' : '' }}>Kejuruan / Inti Program Studi</option>
                        <option value="Umum" {{ old('kelompok') == 'Umum' ? 'selected' : '' }}>Mata Kuliah Umum (MKU)</option>
                        <option value="Muatan Lokal" {{ old('kelompok') == 'Muatan Lokal' ? 'selected' : '' }}>Peminatan / Konsentrasi</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nilai KKM Minimum Kelulusan <span class="text-rose-500">*</span></label>
                    <input type="number" name="kkm" value="{{ old('kkm', 75) }}" required min="0" max="100"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.matakuliah.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Mata Kuliah</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
