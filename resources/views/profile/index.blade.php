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
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-200"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg> Super Admin</span>
                    @elseif($user->role === 'admin' || $user->role === 'petugas')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Admin TU</span>
                    @elseif($user->role === 'guru')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Dewan Guru</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg> Siswa</span>
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
                <h2 class="font-bold text-slate-900 text-sm"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Informasi Profil</h2>
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
                <h2 class="font-bold text-slate-900 text-sm"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Ubah Kata Sandi</h2>
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
