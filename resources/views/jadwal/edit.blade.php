@extends('layouts.app')

@section('title', 'Edit Jadwal Perkuliahan')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.jadwal.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Jadwal Perkuliahan</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Edit Jadwal</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Jadwal Perkuliahan</h1>
            <p class="text-xs text-slate-500">Perbarui alokasi waktu perkuliahan, kelas, mata kuliah, atau dosen pengampu</p>
        </div>
        <a href="{{ route('web.jadwal.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.jadwal.update', $jadwal->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelas Kuliah <span class="text-rose-500">*</span></label>
                    <select name="id_kelas" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('id_kelas', $jadwal->id_kelas) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->kompetensi_keahlian }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari Perkuliahan <span class="text-rose-500">*</span></label>
                    <select name="hari" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Kuliah <span class="text-rose-500">*</span></label>
                    <select name="id_mapel" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ old('id_mapel', $jadwal->id_mapel) == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dosen Pengampu <span class="text-rose-500">*</span></label>
                    <select name="id_guru" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}" {{ old('id_guru', $jadwal->id_guru) == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Ruang Kuliah / Lab</label>
                    <input type="text" name="ruangan" value="{{ old('ruangan', $jadwal->ruangan) }}" placeholder="Contoh: R. Teori 101"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.jadwal.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Perbarui Jadwal</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
