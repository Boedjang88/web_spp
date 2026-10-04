@extends('layouts.app')

@section('title', 'Profil & Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-soft flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 font-black text-xl flex items-center justify-center flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <h1 class="text-xl font-bold text-slate-900">{{ $user->name }}</h1>
                    @if($user->role === 'superadmin')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-200">👑 Super Admin</span>
                    @elseif($user->role === 'admin' || $user->role === 'petugas')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">💼 Admin TU</span>
                    @elseif($user->role === 'guru')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">👨‍🏫 Dewan Guru</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">🎓 Siswa</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400">{{ $user->email }} &bull; Terdaftar sejak {{ $user->created_at ? $user->created_at->format('d F Y') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- 2 Column Settings Form -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Form 1: Biodata Akun -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm">👤 Informasi Profil</h2>
                <p class="text-[11px] text-slate-400">Perbarui nama tampilan dan alamat email login Anda.</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('email')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if($user->guru)
                    <div class="p-3 bg-emerald-50/50 border border-emerald-100 rounded-xl text-xs space-y-1">
                        <span class="font-bold text-emerald-800 block text-[11px]">Terkait Data Guru:</span>
                        <div class="text-slate-600 text-[11px]">NIP: <span class="font-semibold">{{ $user->guru->nip }}</span></div>
                        <div class="text-slate-600 text-[11px]">No. Telp: <span class="font-semibold">{{ $user->guru->no_telp ?? '-' }}</span></div>
                    </div>
                @elseif($user->siswa)
                    <div class="p-3 bg-amber-50/50 border border-amber-100 rounded-xl text-xs space-y-1">
                        <span class="font-bold text-amber-800 block text-[11px]">Terkait Data Siswa:</span>
                        <div class="text-slate-600 text-[11px]">NISN / NIS: <span class="font-semibold">{{ $user->siswa->nisn }} / {{ $user->siswa->nis }}</span></div>
                        <div class="text-slate-600 text-[11px]">Kelas: <span class="font-semibold">{{ $user->siswa->kelas->nama_kelas ?? '-' }}</span></div>
                        <div class="text-slate-600 text-[11px]">Tarif SPP: <span class="font-semibold">Rp {{ number_format($user->siswa->spp->nominal ?? 0, 0, ',', '.') }}</span></div>
                    </div>
                @endif

                <div class="pt-2 text-right">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-xs shadow-sm transition">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Ganti Password Mandiri -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-sm">🔒 Ubah Kata Sandi</h2>
                <p class="text-[11px] text-slate-400">Ganti kata sandi secara mandiri untuk keamanan akun Anda.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" placeholder="Masukkan kata sandi lama..." required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('current_password')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter..." required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('password')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi baru..." required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                </div>

                <div class="pt-2 text-right">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-5 py-2.5 rounded-xl text-xs shadow-sm transition">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
