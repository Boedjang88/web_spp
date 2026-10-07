@extends('layouts.app')

@section('title', 'Tambah Kelas Baru')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tambah Kelas Baru</h1>
            <p class="text-xs text-slate-500">Masukkan nama kelas dan jurusan keahlian</p>
        </div>
        <a href="{{ route('web.kelas.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.kelas.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelas</label>
                <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('nama_kelas') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: IF-3A (Teknik Informatika)">
                @error('nama_kelas')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kompetensi_keahlian" class="block text-xs font-semibold text-slate-700 mb-1">Kompetensi Keahlian (Jurusan)</label>
                <input type="text" id="kompetensi_keahlian" name="kompetensi_keahlian" value="{{ old('kompetensi_keahlian') }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('kompetensi_keahlian') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Rekayasa Perangkat Lunak">
                @error('kompetensi_keahlian')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.kelas.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">Simpan Data Kelas</button>
            </div>
        </form>
    </div>
</div>
@endsection
