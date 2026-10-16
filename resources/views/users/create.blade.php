@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Manajemen Pengguna</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Tambah Baru</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Pengguna Baru</h1>
            <p class="text-xs text-slate-500">Registrasi akun aman berbasis peran dengan perlindungan hak akses ketat (RBAC)</p>
        </div>
        <a href="{{ route('web.users.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form action="{{ route('web.users.store') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block font-semibold text-slate-700 mb-2">
                    Tingkatan Hak Akses (Role) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3.5 flex flex-col items-center text-center gap-1.5 transition hover:border-indigo-400 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600">
                        <input type="radio" name="role" value="superadmin" class="sr-only" {{ old('role') === 'superadmin' ? 'checked' : '' }} onchange="handleRoleChange('superadmin')">
                        <span class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </span>
                        <span class="text-xs font-bold text-slate-800">Super Admin</span>
                        <span class="text-[10px] text-slate-500">Akses Penuh</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3.5 flex flex-col items-center text-center gap-1.5 transition hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                        <input type="radio" name="role" value="admin" class="sr-only" {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} onchange="handleRoleChange('admin')">
                        <span class="p-2 bg-blue-100 text-blue-700 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="text-xs font-bold text-slate-800">Admin TU</span>
                        <span class="text-[10px] text-slate-500">SPP & Master</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3.5 flex flex-col items-center text-center gap-1.5 transition hover:border-emerald-400 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-600">
                        <input type="radio" name="role" value="dosen" class="sr-only" {{ in_array(old('role'), ['dosen', 'guru']) ? 'checked' : '' }} onchange="handleRoleChange('dosen')">
                        <span class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                        <span class="text-xs font-bold text-slate-800">Dosen</span>
                        <span class="text-[10px] text-slate-500">Nilai & Presensi</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3.5 flex flex-col items-center text-center gap-1.5 transition hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50 has-[:checked]:ring-1 has-[:checked]:ring-amber-600">
                        <input type="radio" name="role" value="mahasiswa" class="sr-only" {{ in_array(old('role'), ['mahasiswa', 'siswa']) ? 'checked' : '' }} onchange="handleRoleChange('mahasiswa')">
                        <span class="p-2 bg-amber-100 text-amber-700 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </span>
                        <span class="text-xs font-bold text-slate-800">Mahasiswa</span>
                        <span class="text-[10px] text-slate-500">Portal Mandiri</span>
                    </label>
                </div>
                @error('role')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dynamic Master Data Link -->
            <div id="guruSection" class="{{ in_array(old('role'), ['dosen', 'guru']) ? '' : 'hidden' }} bg-emerald-50/60 p-4 rounded-xl border border-emerald-200 space-y-1.5">
                <label class="block font-semibold text-emerald-900">
                    Hubungkan ke Data Dosen <span class="text-rose-500">*</span>
                </label>
                <select name="id_guru" id="id_guru_select" class="w-full bg-white border border-emerald-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Dosen Pengampu --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('id_guru') == $g->id ? 'selected' : '' }} data-name="{{ $g->nama_guru }}" data-email="{{ $g->email }}">
                            {{ $g->nama_guru }} (NIP/NIDN: {{ $g->nip }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-emerald-700">Akun ini akan otomatis memiliki wewenang untuk mengisi nilai &amp; presensi perkuliahan dosen terkait.</p>
            </div>

            <div id="siswaSection" class="{{ in_array(old('role'), ['mahasiswa', 'siswa']) ? '' : 'hidden' }} bg-amber-50/60 p-4 rounded-xl border border-amber-200 space-y-1.5">
                <label class="block font-semibold text-amber-900">
                    Hubungkan ke Data Mahasiswa <span class="text-rose-500">*</span>
                </label>
                <select name="id_siswa" id="id_siswa_select" class="w-full bg-white border border-amber-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" {{ old('id_siswa') == $s->id ? 'selected' : '' }} data-name="{{ $s->nama }}" data-nisn="{{ $s->nisn }}">
                            {{ $s->nama }} (NIM/NPM: {{ $s->nisn }} - {{ $s->kelas->nama_kelas ?? 'Kelas -' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-amber-700">Akun ini terisolasi mandiri sehingga mahasiswa hanya dapat mengakses nilai, presensi, &amp; tagihan miliknya sendiri.</p>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="nameInput" value="{{ old('name') }}" placeholder="cth: Dr. Ahmad Fauzi, M.Kom" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Alamat Email / Akun Login <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="emailInput" value="{{ old('email') }}" placeholder="cth: fauzi@siakad.ac.id" required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('email')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Password Login <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter..." required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-mono">
                    @error('password')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password..." required
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition font-mono">
                </div>
            </div>

            <!-- Account Status -->
            <div class="flex items-center gap-2.5 pt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                    class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                    Aktifkan akun ini sekarang (Pengguna dapat langsung login ke portal sistem)
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <a href="{{ route('web.users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Simpan Akun Pengguna</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function handleRoleChange(role) {
        const guruSection = document.getElementById('guruSection');
        const siswaSection = document.getElementById('siswaSection');

        if (role === 'dosen' || role === 'guru') {
            guruSection.classList.remove('hidden');
            siswaSection.classList.add('hidden');
        } else if (role === 'mahasiswa' || role === 'siswa') {
            siswaSection.classList.remove('hidden');
            guruSection.classList.add('hidden');
        } else {
            guruSection.classList.add('hidden');
            siswaSection.classList.add('hidden');
        }
    }

    document.getElementById('id_guru_select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            if (opt.dataset.name) document.getElementById('nameInput').value = opt.dataset.name;
            if (opt.dataset.email) document.getElementById('emailInput').value = opt.dataset.email;
        }
    });

    document.getElementById('id_siswa_select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            if (opt.dataset.name) document.getElementById('nameInput').value = opt.dataset.name;
            if (opt.dataset.nisn) document.getElementById('emailInput').value = opt.dataset.nisn + '@mahasiswa.siakad.ac.id';
        }
    });
</script>
@endsection
