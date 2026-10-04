@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <a href="{{ route('web.users.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-700 inline-flex items-center gap-1 mb-1">
                &larr; Kembali ke Manajemen Pengguna
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Akun Pengguna</h1>
            <p class="text-xs text-slate-500">Perbarui hak akses, kata sandi, atau tautan data master akun.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('web.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Tingkatan Hak Akses (Role) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center gap-1.5 transition hover:border-purple-500 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/50">
                        <input type="radio" name="role" value="superadmin" class="sr-only" {{ old('role', $user->role) === 'superadmin' ? 'checked' : '' }} onchange="handleRoleChange('superadmin')">
                        <span class="text-lg">👑</span>
                        <span class="text-xs font-bold text-slate-800">Super Admin</span>
                        <span class="text-[10px] text-slate-400">Akses Penuh</span>
                    </label>
                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center gap-1.5 transition hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="role" value="admin" class="sr-only" {{ in_array(old('role', $user->role), ['admin', 'petugas']) ? 'checked' : '' }} onchange="handleRoleChange('admin')">
                        <span class="text-lg">💼</span>
                        <span class="text-xs font-bold text-slate-800">Admin TU</span>
                        <span class="text-[10px] text-slate-400">SPP & Master</span>
                    </label>
                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center gap-1.5 transition hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                        <input type="radio" name="role" value="guru" class="sr-only" {{ old('role', $user->role) === 'guru' ? 'checked' : '' }} onchange="handleRoleChange('guru')">
                        <span class="text-lg">👨‍🏫</span>
                        <span class="text-xs font-bold text-slate-800">Dewan Guru</span>
                        <span class="text-[10px] text-slate-400">Nilai & Presensi</span>
                    </label>
                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center gap-1.5 transition hover:border-amber-500 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                        <input type="radio" name="role" value="siswa" class="sr-only" {{ old('role', $user->role) === 'siswa' ? 'checked' : '' }} onchange="handleRoleChange('siswa')">
                        <span class="text-lg">🎓</span>
                        <span class="text-xs font-bold text-slate-800">Siswa</span>
                        <span class="text-[10px] text-slate-400">Portal Mandiri</span>
                    </label>
                </div>
                @error('role')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dynamic Master Data Link -->
            <div id="guruSection" class="{{ old('role', $user->role) === 'guru' ? '' : 'hidden' }} bg-emerald-50/60 p-4 rounded-xl border border-emerald-200">
                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1">
                    Hubungkan ke Data Guru
                </label>
                <select name="id_guru" class="w-full bg-white border border-emerald-300 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Guru / Pendidik --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('id_guru', $user->id_guru) == $g->id ? 'selected' : '' }}>
                            {{ $g->nama_guru }} (NIP: {{ $g->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="siswaSection" class="{{ old('role', $user->role) === 'siswa' ? '' : 'hidden' }} bg-amber-50/60 p-4 rounded-xl border border-amber-200">
                <label class="block text-xs font-bold uppercase tracking-wider text-amber-800 mb-1">
                    Hubungkan ke Data Siswa
                </label>
                <select name="id_siswa" class="w-full bg-white border border-amber-300 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" {{ old('id_siswa', $user->id_siswa) == $s->id ? 'selected' : '' }}>
                            {{ $s->nama }} (NISN: {{ $s->nisn }} - {{ $s->kelas->nama_kelas ?? 'Kelas -' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                    @error('email')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password Reset (Optional) -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                <div class="text-xs font-bold text-slate-800">Ubah Kata Sandi (Kosongkan jika tidak ingin mengganti)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                            Password Baru
                        </label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter..."
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        @error('password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru..."
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700">
                    Status Akun Aktif (Izinkan login ke sistem)
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('web.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function handleRoleChange(role) {
        const guruSection = document.getElementById('guruSection');
        const siswaSection = document.getElementById('siswaSection');

        if (role === 'guru') {
            guruSection.classList.remove('hidden');
            siswaSection.classList.add('hidden');
        } else if (role === 'siswa') {
            siswaSection.classList.remove('hidden');
            guruSection.classList.add('hidden');
        } else {
            guruSection.classList.add('hidden');
            siswaSection.classList.add('hidden');
        }
    }
</script>
@endsection
