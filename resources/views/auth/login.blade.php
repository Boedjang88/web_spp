<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIAKAD Academic Workspace</title>
    <!-- Inline Theme Script (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Assets: Vite Compiled Assets with Fallback -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        html.dark body { background-color: #131314; color: #e3e3e3; }
        html:not(.dark) body { background-color: #f8fafd; color: #202124; }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card-in { animation: cardFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 antialiased transition-colors select-text">

    <!-- Top Floating Theme Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button type="button" onclick="toggleTheme()" class="px-3 py-1.5 rounded-xl bg-white dark:bg-[#1e1f20] hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-xs cursor-pointer" title="Ganti Tema">
            <svg class="theme-icon-sun w-3.5 h-3.5 text-amber-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg class="theme-icon-moon w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <span class="theme-label-text">Terang</span>
        </button>
    </div>

    <!-- Sleek Top Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-brand-500 z-9999 opacity-0 pointer-events-none transition-all duration-300 transform -translate-x-full"></div>

    <div class="max-w-md w-full animate-card-in my-6">
        <!-- Logo & Header (Google Workspace Calm Branding) -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-brand-600 text-white rounded-2xl text-base font-bold mb-3 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">SIAKAD Academic Workspace</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-normal">Gunakan akun universitas untuk masuk ke portal terpadu</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-[#1e1f20] rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 md:p-8 shadow-card transition-colors">
            @if(session('error'))
                <div class="mb-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 p-3.5 rounded-xl text-xs text-rose-800 dark:text-rose-200 flex items-start gap-2">
                    <span class="text-rose-600 dark:text-rose-400 font-bold">&bull;</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-3.5 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 flex items-start gap-2">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">&bull;</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">NPM / NIDN / Email Kampus <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@siakad.ac.id') }}" required aria-required="true" autofocus
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        @class([
                            'w-full px-4 py-2.5 bg-white dark:bg-[#131314] border rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 transition',
                            'border-rose-500' => $errors->has('email'),
                            'border-slate-300 dark:border-slate-700' => !$errors->has('email'),
                        ])
                        placeholder="nama@siakad.ac.id">
                    @error('email')
                        <p id="email-error" class="text-xs text-rose-500 mt-1 font-mono" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" value="password123" required aria-required="true"
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        @class([
                            'w-full px-4 py-2.5 bg-white dark:bg-[#131314] border rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 transition',
                            'border-rose-500' => $errors->has('password'),
                            'border-slate-300 dark:border-slate-700' => !$errors->has('password'),
                        ])
                        placeholder="••••••••">
                    @error('password')
                        <p id="password-error" class="text-xs text-rose-500 mt-1 font-mono" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-brand-600 focus:ring-brand-500 mr-2">
                        Ingat Saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:scale-[0.98] text-white text-xs font-semibold rounded-xl transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Masuk ke Akun</span>
                </button>

                <!-- Google SSO Button (Clean Workspace SSO) -->
                <div class="pt-2">
                    <a href="{{ route('auth.google') }}" class="w-full py-2.5 px-4 bg-white dark:bg-[#131314] hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-medium rounded-xl border border-slate-300 dark:border-slate-700 transition shadow-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/><path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.4 0 15.3c0 2.9.7 5.6 1.9 8l3.7-2.9z"/><path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/></svg>
                        <span>Masuk dengan Google Workspace (SSO)</span>
                    </a>
                </div>
            </form>

            <!-- PMB Banner / New Student Registration Link -->
            <div class="mt-5 pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">Calon Mahasiswa Baru (PMB)?</p>
                <a href="{{ route('pmb.register') }}" class="inline-flex items-center justify-center w-full py-2 px-4 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold rounded-xl transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Pendaftaran Mahasiswa Baru</span>
                </a>
            </div>

            <!-- Quick Account Selector Demo -->
            <div class="mt-5 pt-4 border-t border-slate-200 dark:border-slate-800">
                <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold block mb-2">Pilih Cepat Akun Demo:</span>
                <div class="grid grid-cols-3 gap-2 text-[10px]">
                    <button type="button" onclick="fillLogin('admin@siakad.ac.id')" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-brand-50 dark:hover:bg-brand-950/50 active:scale-[0.97] border border-slate-200 dark:border-slate-700 hover:border-brand-300 text-slate-700 dark:text-slate-300 hover:text-brand-700 dark:hover:text-brand-300 transition text-center cursor-pointer">
                        <strong class="block">Admin BAAK</strong>
                    </button>
                    <button type="button" onclick="fillLogin('dosen@siakad.ac.id')" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-brand-50 dark:hover:bg-brand-950/50 active:scale-[0.97] border border-slate-200 dark:border-slate-700 hover:border-brand-300 text-slate-700 dark:text-slate-300 hover:text-brand-700 dark:hover:text-brand-300 transition text-center cursor-pointer">
                        <strong class="block">Dosen</strong>
                    </button>
                    <button type="button" onclick="fillLogin('mahasiswa@siakad.ac.id')" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-brand-50 dark:hover:bg-brand-950/50 active:scale-[0.97] border border-slate-200 dark:border-slate-700 hover:border-brand-300 text-slate-700 dark:text-slate-300 hover:text-brand-700 dark:hover:text-brand-300 transition text-center cursor-pointer">
                        <strong class="block">Mahasiswa</strong>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center text-xs text-slate-400 dark:text-slate-500 font-mono mt-4">
            SIAKAD Academic Workspace &bull; Enterprise Edition
        </div>
    </div>

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('.theme-icon-sun').forEach(el => {
                if (isDark) el.classList.remove('hidden'); else el.classList.add('hidden');
            });
            document.querySelectorAll('.theme-icon-moon').forEach(el => {
                if (isDark) el.classList.add('hidden'); else el.classList.remove('hidden');
            });
            document.querySelectorAll('.theme-label-text').forEach(el => {
                el.textContent = isDark ? 'Gelap' : 'Terang';
            });
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcons);

        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }

        document.addEventListener('submit', function(e) {
            if (e.defaultPrevented) return;
            const form = e.target;

            // Guard against invalid HTML5 form submissions
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.setAttribute('aria-busy', 'true');
                const origText = submitBtn.innerHTML;
                submitBtn.dataset.originalHtml = origText;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" role="status" aria-label="Memproses Masuk">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses Masuk...</span>
                `;

                let isNavigating = false;
                const onBeforeUnload = () => { isNavigating = true; };
                window.addEventListener('beforeunload', onBeforeUnload, { once: true });

                setTimeout(() => {
                    window.removeEventListener('beforeunload', onBeforeUnload);
                    if (!isNavigating && submitBtn && submitBtn.disabled && submitBtn.dataset.originalHtml) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.removeAttribute('aria-busy');
                        submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                        delete submitBtn.dataset.originalHtml;
                    }
                }, 12000);
            }
        });

        // Re-enable button if browser constraint validation flags invalid input
        document.addEventListener('invalid', function(e) {
            const form = e.target.form;
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && submitBtn.disabled && submitBtn.dataset.originalHtml) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.removeAttribute('aria-busy');
                    submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                    delete submitBtn.dataset.originalHtml;
                }
            }
        }, true);
    </script>
</body>
</html>
