@extends('layouts.app')

@section('title', 'Edit Mahasiswa - ' . $siswa->nama)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.mahasiswa.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Data Mahasiswa</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('web.mahasiswa.show', $siswa->id) }}" class="text-xs font-medium text-slate-500 hover:text-slate-800">{{ $siswa->nama }}</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Edit Data</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Biodata Mahasiswa</h1>
            <p class="text-xs text-slate-500">Perbarui biodata dan data akademik {{ $siswa->nama }}</p>
        </div>
        <a href="{{ route('web.mahasiswa.show', $siswa->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.mahasiswa.update', $siswa->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NISN -->
                <div>
                    <label for="nisn" class="block text-xs font-semibold text-slate-700 mb-1">NIM / NISN (10 Digit) <span class="text-rose-500">*</span></label>
                    <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required maxlength="10"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('nisn') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white font-mono transition">
                    @error('nisn')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIS -->
                <div>
                    <label for="nis" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Induk Mahasiswa (NIS/NPM)</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required maxlength="8"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('nis') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white font-mono transition">
                    @error('nis')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama Siswa -->
            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Mahasiswa <span class="text-rose-500">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('nama') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                @error('nama')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kelas -->
                <div>
                    <label for="id_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Kelas Perkuliahan <span class="text-rose-500">*</span></label>
                    <select id="id_kelas" name="id_kelas" required
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('id_kelas') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('id_kelas', $siswa->id_kelas) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->kompetensi_keahlian }})</option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif SPP -->
                <div>
                    <label for="id_spp" class="block text-xs font-semibold text-slate-700 mb-1">Tarif UKT / Biaya Kuliah <span class="text-rose-500">*</span></label>
                    <select id="id_spp" name="id_spp" required
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('id_spp') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        @foreach($sppList as $s)
                            <option value="{{ $s->id }}" {{ old('id_spp', $siswa->id_spp) == $s->id ? 'selected' : '' }}>Tahun {{ $s->tahun }} - Rp {{ number_format($s->nominal, 0, ',', '.') }}/bln</option>
                        @endforeach
                    </select>
                    @error('id_spp')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- No. Telepon -->
            <div>
                <label for="no_telp" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $siswa->no_telp) }}" required
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('no_telp') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white font-mono transition">
                @error('no_telp')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal <span class="text-rose-500">*</span></label>
                <textarea id="alamat" name="alamat" rows="3" required
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('alamat') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">{{ old('alamat', $siswa->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.mahasiswa.show', $siswa->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Perbarui Data Mahasiswa</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
