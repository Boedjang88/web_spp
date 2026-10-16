@extends('layouts.app')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.jadwal.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Jadwal Perkuliahan</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Tambah Baru</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Jadwal Perkuliahan</h1>
            <p class="text-xs text-slate-500">Alokasikan waktu perkuliahan, kelas kuliah, mata kuliah, dan dosen pengampu</p>
        </div>
        <a href="{{ route('web.jadwal.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.jadwal.store') }}" class="space-y-4 text-xs">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelas Kuliah <span class="text-rose-500">*</span></label>
                    <select name="id_kelas" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Kelas Kuliah --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->kompetensi_keahlian }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari Perkuliahan <span class="text-rose-500">*</span></label>
                    <select name="hari" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Kuliah <span class="text-rose-500">*</span></label>
                    <select name="id_mapel" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ old('id_mapel') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dosen Pengampu <span class="text-rose-500">*</span></label>
                    <select name="id_guru" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">-- Pilih Dosen Pengampu --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}" {{ old('id_guru') == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '07:30') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '09:00') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Ruang Kuliah / Lab</label>
                    <input type="text" name="ruangan" value="{{ old('ruangan', 'R. Teori 101') }}" placeholder="Contoh: R. Teori 101 / Lab Komputer"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.jadwal.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Jadwal Perkuliahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
