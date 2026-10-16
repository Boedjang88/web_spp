@extends('layouts.app')

@section('title', 'Tambah Dosen Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.dosen.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Data Dosen</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Tambah Baru</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Dosen Pengajar Baru</h1>
            <p class="text-xs text-slate-500">Registrasi tenaga pendidik, NIDN/NIP, kontak, dan biodata dosen</p>
        </div>
        <a href="{{ route('web.dosen.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.dosen.store') }}" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-semibold text-slate-700 mb-1">NIDN / NIP (Nomor Induk Pegawai/Dosen)</label>
                <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 198501152010011002"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Dosen (Beserta Gelar) <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_guru" value="{{ old('nama_guru') }}" required placeholder="Contoh: Dr. Budi Santoso, S.Kom., M.T."
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="jenis_kelamin" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp') }}" placeholder="081234567890"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Email Institusi</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="dosen@kampus.ac.id"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alamat Domisili</label>
                <textarea name="alamat" rows="3" placeholder="Alamat domisili dosen..."
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">{{ old('alamat') }}</textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.dosen.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Data Dosen</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
