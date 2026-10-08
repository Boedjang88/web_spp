@extends('layouts.app')

@section('title', 'Ubah Password Akun')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-soft flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Ubah Password Akun</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola dan perbarui kata sandi akun Anda secara mandiri demi keamanan.</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center font-bold text-base flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
    </div>

    <!-- Form: Ganti Password Mandiri -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-5">
        <div class="border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm"><svg class="w-4 h-4 inline-block text-indigo-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Form Ubah Kata Sandi</h2>
            <p class="text-[11px] text-slate-400">Pastikan kata sandi baru Anda unik, aman, dan mudah diingat.</p>
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
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-xs shadow-sm transition">
                    Perbarui Kata Sandi Akun
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
