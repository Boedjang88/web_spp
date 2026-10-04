@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Kelas</h1>
            <p class="text-xs text-slate-500">Perbarui data kelas {{ $kelas->nama_kelas }}</p>
        </div>
        <a href="{{ route('web.kelas.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.kelas.update', $kelas->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelas</label>
                <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('nama_kelas') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama_kelas')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kompetensi_keahlian" class="block text-xs font-semibold text-slate-700 mb-1">Kompetensi Keahlian</label>
                <input type="text" id="kompetensi_keahlian" name="kompetensi_keahlian" value="{{ old('kompetensi_keahlian', $kelas->kompetensi_keahlian) }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('kompetensi_keahlian') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('kompetensi_keahlian')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.kelas.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">Perbarui Kelas</button>
            </div>
        </form>
    </div>
</div>
@endsection
