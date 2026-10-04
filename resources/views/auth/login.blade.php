<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIAKAD Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Subtle Background Elements -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 text-white rounded-2xl shadow-xl shadow-indigo-600/30 text-2xl mb-3 border border-indigo-400/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">SIAKAD Enterprise</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem Informasi Akademik &amp; Keuangan Perguruan Tinggi</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-8">

            @if(session('error'))
                <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-3.5 rounded-xl text-xs text-rose-700 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-xl text-xs text-emerald-700 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email / NPM / NIDN</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@siakad.ac.id') }}" required autofocus
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition"
                            placeholder="nama@siakad.ac.id">
                        <div class="absolute left-3 top-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" value="password123" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition"
                            placeholder="••••••••">
                        <div class="absolute left-3 top-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Public Check -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        Ingat Saya
                    </label>
                    <a href="{{ route('cek.index') }}" class="text-indigo-600 hover:underline font-semibold">Cek Tagihan Publik &rarr;</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-xl transition duration-150 flex items-center justify-center gap-2">
                    <span>Masuk Portal SIAKAD</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- Quick Account Selector Demo -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center mb-2.5">Akun Default Demo Universitas</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="document.getElementById('email').value='admin@siakad.ac.id';document.getElementById('password').value='password123';"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 border border-slate-200 text-left transition group">
                        <span class="font-bold text-slate-800 group-hover:text-indigo-700 block">Admin BAAK</span>
                        <span class="text-[10px] text-slate-500 block truncate">admin@siakad.ac.id</span>
                    </button>
                    <button type="button" onclick="document.getElementById('email').value='petugas@siakad.ac.id';document.getElementById('password').value='password123';"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 border border-slate-200 text-left transition group">
                        <span class="font-bold text-slate-800 group-hover:text-indigo-700 block">Petugas Keuangan</span>
                        <span class="text-[10px] text-slate-500 block truncate">petugas@siakad.ac.id</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-[11px] text-slate-400">
            &copy; {{ date('Y') }} SIAKAD Enterprise Portal &bull; Perguruan Tinggi Sistem Terpadu
        </div>
    </div>

</body>
</html>
