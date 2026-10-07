<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIAKAD Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        navy: {
                            800: '#1c2541',
                            900: '#0b132b',
                            950: '#070d1e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b132b; color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card-in { animation: cardFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="bg-[#0b132b] min-h-screen flex items-center justify-center p-4 antialiased text-slate-100">

    <!-- Sleek Top Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400 z-[9999] opacity-0 pointer-events-none transition-all duration-300 transform -translate-x-full"></div>

    <div class="max-w-md w-full animate-card-in my-6">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-2xl text-sm font-bold mb-3 shadow-lg shadow-blue-500/20 ring-4 ring-blue-500/10">
                SIAKAD
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">SIAKAD ENTERPRISE</h1>
            <p class="text-xs text-blue-200/80 mt-1 font-medium">Sistem Informasi Akademik &amp; Keuangan Universitas</p>
        </div>

        <!-- Login Card -->
        <div class="bg-[#1c2541] rounded-2xl border border-blue-900/50 p-6 md:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            @if(session('error'))
                <div class="mb-4 bg-rose-950/40 border border-rose-800/80 p-3.5 rounded-xl text-xs text-rose-200 flex items-start gap-2">
                    <span class="text-rose-400 font-bold">&bull;</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-emerald-950/40 border border-emerald-800/80 p-3.5 rounded-xl text-xs text-emerald-200 flex items-start gap-2">
                    <span class="text-emerald-400 font-bold">&bull;</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-blue-100 mb-1.5 font-mono">NPM / NIDN / Email Kampus</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@siakad.ac.id') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition"
                        placeholder="nama@siakad.ac.id">
                    @error('email')
                        <p class="text-xs text-rose-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-blue-100 mb-1.5 font-mono">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password123" required
                        class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-xs text-rose-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Public Check -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-[#0b132b] text-blue-600 focus:ring-blue-500 mr-2">
                        Ingat Saya
                    </label>
                    <a href="{{ route('cek.index') }}" class="text-blue-400 hover:text-blue-300 hover:underline transition font-medium">Cek Tagihan &rarr;</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 active:scale-[0.98] text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk Portal SIAKAD
                </button>
            </form>

            <!-- PMB Banner / New Student Registration Link -->
            <div class="mt-5 pt-4 border-t border-slate-700/60 text-center">
                <p class="text-xs text-slate-300 mb-2 font-medium">Calon Mahasiswa Baru (PMB)?</p>
                <a href="{{ route('pmb.register') }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-600/20 gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Pendaftaran Mahasiswa Baru &amp; Isi Biodata
                </a>
            </div>

            <!-- Quick Account Selector Demo -->
            <div class="mt-5 pt-4 border-t border-slate-700/60">
                <span class="text-[10px] uppercase tracking-wider text-blue-300/70 font-mono font-semibold block mb-2">Akun Uji Coba Quick Access:</span>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-mono">
                    <button type="button" onclick="fillLogin('admin@siakad.ac.id')" class="p-2 rounded-lg bg-[#0b132b] hover:bg-blue-900/50 active:scale-[0.97] border border-blue-900/80 text-blue-200 transition text-center">
                        <strong class="block text-white">Admin BAAK</strong>
                    </button>
                    <button type="button" onclick="fillLogin('dosen@siakad.ac.id')" class="p-2 rounded-lg bg-[#0b132b] hover:bg-blue-900/50 active:scale-[0.97] border border-blue-900/80 text-blue-200 transition text-center">
                        <strong class="block text-white">Dosen</strong>
                    </button>
                    <button type="button" onclick="fillLogin('mahasiswa@siakad.ac.id')" class="p-2 rounded-lg bg-[#0b132b] hover:bg-blue-900/50 active:scale-[0.97] border border-blue-900/80 text-blue-200 transition text-center">
                        <strong class="block text-white">Mahasiswa</strong>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center text-xs text-blue-300/60 font-mono mt-4">
            Universitas SIAKAD Enterprise &bull; System v2.6 &bull; Deep Ocean Blue
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>
