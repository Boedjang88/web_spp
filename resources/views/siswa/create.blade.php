@extends('layouts.app')

@section('title', 'Registrasi Siswa Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Registrasi Siswa Baru</h1>
            <p class="text-xs text-slate-500">Lengkapi biodata siswa, penempatan kelas, dan tarif SPP</p>
        </div>
        <a href="{{ route('web.siswa.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.siswa.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NISN -->
                <div>
                    <label for="nisn" class="block text-xs font-semibold text-slate-700 mb-1">NISN (10 Digit)</label>
                    <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                        class="w-full px-3.5 py-2 text-xs border @error('nisn') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 0051234567">
                    @error('nisn')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIS -->
                <div>
                    <label for="nis" class="block text-xs font-semibold text-slate-700 mb-1">NIS (Nomor Induk Siswa)</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required maxlength="8"
                        class="w-full px-3.5 py-2 text-xs border @error('nis') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 2122001">
                    @error('nis')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama Siswa -->
            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('nama') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Ahmad Fauzi Pratama">
                @error('nama')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kelas -->
                <div>
                    <label for="id_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Kelas</label>
                    <select id="id_kelas" name="id_kelas" required
                        class="w-full px-3.5 py-2 text-xs border @error('id_kelas') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->kompetensi_keahlian }})</option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif SPP -->
                <div>
                    <label for="id_spp" class="block text-xs font-semibold text-slate-700 mb-1">Tarif SPP</label>
                    <select id="id_spp" name="id_spp" required
                        class="w-full px-3.5 py-2 text-xs border @error('id_spp') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Tarif SPP --</option>
                        @foreach($sppList as $s)
                            <option value="{{ $s->id }}" {{ old('id_spp') == $s->id ? 'selected' : '' }}>Tahun {{ $s->tahun }} - Rp {{ number_format($s->nominal, 0, ',', '.') }}/bln</option>
                        @endforeach
                    </select>
                    @error('id_spp')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- No. Telepon -->
            <div>
                <label for="no_telp" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('no_telp') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 081234567890">
                @error('no_telp')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                <textarea id="alamat" name="alamat" rows="3" required
                    class="w-full px-3.5 py-2 text-xs border @error('alamat') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan alamat lengkap siswa...">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.siswa.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">Simpan Data Siswa</button>
            </div>
        </form>
    </div>
</div>
@endsection
