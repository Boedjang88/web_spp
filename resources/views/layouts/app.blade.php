<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>@yield('title', 'SIAKAD Enterprise') - Sistem Informasi Akademik &amp; Keuangan</title>
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b132b">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').catch(function(err) {});
            });
        }
    </script>

    <!-- Inline Theme Script (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Assets: Vite Compiled Assets with Prototyping Fallback -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
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
                                200: '#bfdbfe',
                                300: '#93c5fd',
                                400: '#60a5fa',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                                950: '#172554',
                            }
                        },
                        boxShadow: {
                            'soft': '0 1px 3px 0 rgba(15, 23, 42, 0.05)',
                            'card': '0 4px 6px -1px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.05)',
                            'floating': '0 10px 25px -5px rgba(15, 23, 42, 0.2)',
                            'toast': '0 4px 14px rgba(15, 23, 42, 0.15)',
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- Preconnect to Google Fonts for Fast Non-Blocking Delivery -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">

    <!-- Alpine.js CDN for interactive tabs & widgets -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        html.dark body {
            background-color: #0b132b;
            color: #f8fafc;
        }
        html:not(.dark) body {
            background-color: #f8fafc;
            color: #0f172a;
        }

        /* WCAG 2.1 AA Contrast for Table Headers */
        table thead th {
            color: #334155;
            font-weight: 700;
        }
        html.dark table thead th {
            color: #cbd5e1;
            font-weight: 700;
        }

        /* Standardized High-Contrast Focus Rings (WCAG 2.2 AA SC 2.4.7 & 2.4.11) */
        button:focus-visible, a:focus-visible, [role="button"]:focus-visible, [tabindex]:focus-visible {
            outline: 2px solid #2563eb !important;
            outline-offset: 2px !important;
        }
        html.dark button:focus-visible, html.dark a:focus-visible, html.dark [role="button"]:focus-visible, html.dark [tabindex]:focus-visible {
            outline: 2px solid #3b82f6 !important;
            outline-offset: 2px !important;
        }
        input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: none !important;
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #2563eb !important;
        }
        html.dark input:focus-visible, html.dark select:focus-visible, html.dark textarea:focus-visible {
            box-shadow: 0 0 0 2px #0f172a, 0 0 0 4px #3b82f6 !important;
        }

        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

        @keyframes toastSlideIn {
            from { transform: translateY(-12px) scale(0.96); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes toastFadeOut {
            from { transform: translateY(0) scale(1); opacity: 1; }
            to { transform: translateY(-12px) scale(0.96); opacity: 0; }
        }
        @keyframes modalEnter {
            from { transform: scale(0.94) translateY(10px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        @keyframes modalLeave {
            from { transform: scale(1) translateY(0); opacity: 1; }
            to { transform: scale(0.94) translateY(10px); opacity: 0; }
        }
        @keyframes modalBackdropEnter {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes modalBackdropLeave {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-toast-in { animation: toastSlideIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-toast-out { animation: toastFadeOut 0.15s ease-in forwards; }
        .animate-modal-enter { animation: modalEnter 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-modal-leave { animation: modalLeave 0.15s ease-in forwards; }
        .animate-modal-backdrop { animation: modalBackdropEnter 0.2s ease-out forwards; }
        .animate-backdrop-leave { animation: modalBackdropLeave 0.15s ease-in forwards; }
        .animate-card-in { animation: cardFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Global Fluid Micro-Interactions */
        a, button, input, select, textarea, [role="button"] {
            transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease, transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }

        button:active, a.btn:active, [role="button"]:active {
            transform: scale(0.98);
        }

        /* Native CSS View Transitions API & Smooth Page Navigation */
        @view-transition {
            navigation: auto;
        }

        ::view-transition-old(root) {
            animation: 120ms ease-out cubic-bezier(0.4, 0, 1, 1) both pageExit;
        }
        ::view-transition-new(root) {
            animation: 200ms ease-in cubic-bezier(0, 0, 0.2, 1) both pageEnter;
        }

        @keyframes pageExit {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-4px) scale(0.995); }
        }
        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-exit-active {
            opacity: 0 !important;
            transform: translateY(-6px) scale(0.995) !important;
            transition: opacity 0.12s ease-out, transform 0.12s ease-out !important;
        }

        /* Mobile Touch & Responsive Table Scroll Optimizations */
        * { -webkit-tap-highlight-color: transparent; }
        html, body { touch-action: manipulation; }
        
        @media (max-width: 640px) {
            button, input, select, textarea, a.btn, [role="button"] {
                min-height: 40px;
            }
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col md:flex-row select-text">

    <!-- Skip to main content link for keyboard users (WCAG 2.4.1) -->
    <a href="#main-content" 
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-9999 focus:px-4 focus:py-2.5 focus:bg-brand-600 focus:text-white focus:text-xs focus:font-bold focus:rounded-xl focus:shadow-xl focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900 transition select-none">
        Lewati ke Konten Utama &rarr;
    </a>

    <!-- Top Sleek Page Loading Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-linear-to-r from-brand-500 via-indigo-500 to-emerald-400 z-9999 opacity-0 pointer-events-none transition-all duration-300 transform -translate-x-full"></div>

    <!-- Floating Toast Notification Container (WCAG 4.1.3 Status Messages) -->
    <div id="toastContainer" role="region" aria-label="Notifikasi Sistem" aria-live="polite" aria-atomic="false" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-6 z-50 flex flex-col gap-2.5 max-w-md w-auto sm:w-96 pointer-events-none"></div>

    <!-- Mobile Header Bar -->
    <header class="md:hidden text-white px-4 py-3 flex justify-between items-center sticky top-0 z-40 shadow-sm border-b border-slate-800/80 backdrop-blur-md bg-slate-950/95">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-xl bg-brand-600 flex items-center justify-center font-bold text-white shadow-sm ring-1 ring-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div>
                <span class="font-extrabold text-xs tracking-tight text-white block">SIAKAD ENTERPRISE</span>
                <span class="text-[9px] text-brand-300 font-medium block -mt-0.5">Universitas &bull; Portal Akademik</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Mobile Theme Switcher Button -->
            <button type="button" onclick="toggleTheme()" class="w-9 h-9 min-w-9 min-h-9 rounded-xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 flex items-center justify-center transition active:scale-95" title="Ganti Tema" aria-label="Ganti tema tampilan">
                <svg class="theme-icon-sun w-4 h-4 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="theme-icon-moon w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            @auth
            <a href="{{ route('profile.index') }}" class="w-9 h-9 min-w-9 min-h-9 rounded-xl bg-slate-900 text-brand-300 border border-slate-800 flex items-center justify-center font-bold text-xs overflow-hidden" title="Profil" aria-label="Profil Pengguna">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </a>
            @endauth
            <button type="button" onclick="toggleMobileMenu()" class="p-2 min-w-9 min-h-9 flex items-center justify-center rounded-xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 active:scale-95 transition" aria-label="Buka Menu Navigasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </header>

    <!-- Mobile Drawer Sidebar Backdrop -->
    <div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-in-out" onclick="toggleMobileMenu(true)" role="dialog" aria-modal="true" aria-label="Menu Navigasi">
        <div id="mobileDrawerContent" class="w-72 max-w-[85vw] bg-slate-950 h-full p-5 text-slate-200 overflow-y-auto flex flex-col justify-between shadow-2xl border-r border-slate-800 transform -translate-x-full transition-transform duration-300 ease-in-out" onclick="event.stopPropagation()">
            <div>
                <div class="flex justify-between items-center mb-5 border-b border-slate-800/80 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-600 flex items-center justify-center font-bold text-white shadow-sm ring-1 ring-white/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">SIAKAD Enterprise</span>
                            <span class="text-[9px] text-slate-400 font-mono">v2.6 &bull; Cloud</span>
                        </div>
                    </div>
                    <button type="button" onclick="toggleMobileMenu()" class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-slate-900 border border-slate-800" aria-label="Tutup Menu Navigasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                @auth
                <!-- Mobile User Profile Card -->
                <div class="mb-4 p-3 bg-slate-900 rounded-xl border border-slate-800 flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-brand-600/30 border border-brand-400/40 text-brand-300 font-bold flex items-center justify-center text-xs shrink-0 overflow-hidden">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-xs text-white block truncate">{{ auth()->user()->siswa?->nama ?? auth()->user()->guru?->nama_guru ?? auth()->user()->name }}</span>
                        <span class="text-[10px] text-brand-300 font-mono block">{{ auth()->user()->siswa?->nisn ?? auth()->user()->siswa?->nis ?? auth()->user()->guru?->nip ?? auth()->user()->dosen?->nidn ?? auth()->user()->email }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold font-mono bg-brand-500/20 text-brand-200 border border-brand-500/30 uppercase inline-block mt-0.5">{{ ucfirst(auth()->user()->role ?? 'Mahasiswa') }}</span>
                    </div>
                </div>
                @endauth

                <!-- Reusable Mobile Navigation Menu -->
                <x-navigation.menu-items :mobile="true" />
            </div>

            <!-- Mobile Logout -->
            @auth
            <div class="pt-4 mt-6 border-t border-slate-800/80">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>

    <!-- Desktop Sidebar (Modern Dark Slate + Brand Accents) -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-950 text-slate-300 h-screen sticky top-0 border-r border-slate-800/60 z-30 select-none shrink-0">
        
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800/60 flex items-center space-x-3 bg-slate-950">
            <div class="w-9 h-9 rounded-xl bg-linear-to-tr from-brand-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-md ring-1 ring-white/20 shrink-0">
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div class="truncate">
                <span class="font-extrabold text-sm text-white tracking-tight block truncate">SIAKAD Enterprise</span>
                <span class="text-[10px] text-brand-300 font-semibold uppercase tracking-wider block">Universitas &bull; Portal</span>
            </div>
        </div>

        @auth
        <!-- User Profile Card -->
        <div class="p-4 bg-slate-900/90 border-b border-slate-800/60 space-y-1">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-brand-600/30 border border-brand-400/40 text-brand-300 font-bold flex items-center justify-center text-xs shrink-0 overflow-hidden">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="truncate">
                    <span class="font-bold text-xs text-white block truncate">{{ auth()->user()->siswa?->nama ?? auth()->user()->guru?->nama_guru ?? auth()->user()->name }}</span>
                    <span class="text-[10px] text-brand-300 font-mono block">{{ auth()->user()->siswa?->nisn ?? auth()->user()->siswa?->nis ?? auth()->user()->guru?->nip ?? auth()->user()->dosen?->nidn ?? auth()->user()->email }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold font-mono bg-brand-500/20 text-brand-200 border border-brand-500/30 uppercase inline-block mt-0.5">{{ ucfirst(auth()->user()->role ?? 'Mahasiswa') }}</span>
                </div>
            </div>
        </div>
        @endauth

        <!-- Sidebar Nav Links (Single Source of Truth) -->
        <div class="flex-1 px-3 py-4 space-y-4 overflow-y-auto custom-scrollbar text-xs">
            <x-navigation.menu-items />
        </div>

        <!-- User Profile Footer -->
        @auth
        <div class="p-3 border-t border-slate-800/60 bg-slate-950">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <a href="{{ route('profile.index') }}" class="flex items-center space-x-2.5 overflow-hidden flex-1 group">
                    <div class="w-7 h-7 rounded-lg bg-brand-500/20 text-brand-300 font-bold flex items-center justify-center text-xs shrink-0 group-hover:bg-brand-500/30 transition overflow-hidden">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-xs text-white block truncate group-hover:text-brand-300 transition">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-400 font-mono uppercase">{{ auth()->user()->role ?? 'mahasiswa' }}</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition" title="Keluar" aria-label="Keluar dari akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    <!-- Main Content Wrapper (Fixed: No pb-24 empty void, zero dock overlap) -->
    <div class="flex-1 flex flex-col min-h-screen pb-8 md:pb-0 overflow-x-hidden">
        
        <!-- Desktop Header Bar -->
        <header class="hidden md:flex bg-white/90 dark:bg-slate-900/90 border-b border-slate-200/80 dark:border-slate-800 px-8 py-3.5 justify-between items-center sticky top-0 z-20 shadow-soft backdrop-blur-md">
            <div class="flex items-center space-x-3 text-xs">
                <span class="font-semibold text-slate-500 dark:text-slate-400">SIAKAD Enterprise &bull; {{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Theme Switcher Button -->
                <button type="button" onclick="toggleTheme()" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-slate-200/60 dark:border-slate-700 cursor-pointer select-none" title="Ganti Tema (Terang / Gelap)" aria-label="Ganti tema tampilan">
                    <svg class="theme-icon-sun w-3.5 h-3.5 text-amber-500 dark:text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="theme-icon-moon w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span>Tema: <strong class="theme-label-text">Terang</strong></span>
                </button>

                @if(auth()->check() && in_array(auth()->user()->role, ['superadmin', 'admin', 'petugas', 'baak']))
                <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-brand-50 dark:bg-brand-900/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-brand-200/60 dark:border-brand-800/60">
                    <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>API Console</span>
                </a>
                @endif
            </div>
        </header>

        <!-- Main Body (WCAG 2.4.1 Target) -->
        <main id="main-content" tabindex="-1" class="flex-1 p-4 sm:p-6 md:p-8 max-w-7xl w-full mx-auto space-y-6 animate-card-in outline-none">

            {{-- Inline Flash Alert Banner (WCAG accessible, dismissible) --}}
            @if(session('success'))
                <x-alert type="success" :dismissible="true">
                    {{ session('success') }}
                </x-alert>
            @endif
            @if(session('error'))
                <x-alert type="danger" :dismissible="true">
                    {{ session('error') }}
                </x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info" :dismissible="true">
                    {{ session('info') }}
                </x-alert>
            @endif
            @if(session('warning'))
                <x-alert type="warning" :dismissible="true">
                    {{ session('warning') }}
                </x-alert>
            @endif

            @yield('content')
        </main>

        <!-- Desktop Footer -->
        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 py-4 px-8 text-center text-xs text-slate-400 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>&copy; {{ date('Y') }} SIAKAD Enterprise &bull; Sistem Informasi Akademik &amp; Keuangan Terpadu</span>
                <span class="font-mono text-[11px] text-slate-400">Laravel 11.x &bull; 4-Tier RBAC &bull; UU PDP Compliant</span>
            </div>
        </footer>

    </div>

    <!-- Global Modal Alert Dialog (WCAG 2.4.3 & 4.1.2) -->
    <div id="alertModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 transition-opacity duration-150 animate-modal-backdrop" role="alertdialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalBody">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl max-w-md w-full p-5 shadow-2xl space-y-4 animate-modal-enter">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div id="modalIconContainer" class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-mono font-bold text-xs shrink-0" aria-hidden="true">
                        !
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Notifikasi Sistem</h3>
                        <span id="modalTypeBadge" class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider">ALERT</span>
                    </div>
                </div>
                <button type="button" onclick="closeAlertModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-sm font-mono transition p-1 rounded-lg focus:ring-2 focus:ring-brand-500" aria-label="Tutup dialog notifikasi">&times;</button>
            </div>
            <div id="modalBody" class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans border-y border-zinc-100 dark:border-zinc-800 py-3 max-h-60 overflow-y-auto">
                Pesan notifikasi sistem.
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAlertModal()" class="px-4 py-2 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 active:scale-[0.97] rounded-lg text-xs font-semibold font-mono transition-all duration-150 focus:ring-2 focus:ring-brand-500">
                    Tutup &bull; OK
                </button>
            </div>
        </div>
    </div>

    <!-- Global Toast & Interactive Feedback System -->
    <script>
        function toggleMobileMenu(forceClose = false) {
            const sidebar = document.getElementById('mobileSidebar');
            const drawer = document.getElementById('mobileDrawerContent');
            if (!sidebar || !drawer) return;

            const isOpening = sidebar.classList.contains('hidden') && !forceClose;
            
            if (isOpening) {
                sidebar.classList.remove('hidden');
                requestAnimationFrame(() => {
                    sidebar.classList.remove('opacity-0');
                    sidebar.classList.add('opacity-100');
                    drawer.classList.remove('-translate-x-full');
                    drawer.classList.add('translate-x-0');
                });
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.remove('opacity-100');
                sidebar.classList.add('opacity-0');
                drawer.classList.remove('translate-x-0');
                drawer.classList.add('-translate-x-full');
                document.body.classList.remove('overflow-hidden');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }

        let alertModalTriggerEl = null;

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const alertModal = document.getElementById('alertModal');
                if (alertModal && !alertModal.classList.contains('hidden')) {
                    closeAlertModal();
                    return;
                }
                const openModal = document.querySelector('[role="dialog"][aria-modal="true"]:not(#alertModal):not(#mobileSidebar)');
                if (openModal && openModal.style.display !== 'none' && !openModal.classList.contains('hidden')) {
                    return;
                }
                const mobileSidebar = document.getElementById('mobileSidebar');
                if (mobileSidebar && !mobileSidebar.classList.contains('hidden')) {
                    toggleMobileMenu(true);
                }
            }
        });

        function showAlertModal(title, message, type = 'error') {
            const modal = document.getElementById('alertModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalIconContainer = document.getElementById('modalIconContainer');
            const modalTypeBadge = document.getElementById('modalTypeBadge');

            if (!modal) return;

            alertModalTriggerEl = document.activeElement;
            document.body.classList.add('overflow-hidden');
            modalTitle.textContent = title || (type === 'error' ? 'Gagal / Error' : 'Informasi');
            modalBody.innerHTML = typeof message === 'string' ? message : JSON.stringify(message);
            modalTypeBadge.textContent = type.toUpperCase();

            if (type === 'error') {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '✕';
            } else if (type === 'success') {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '✓';
            } else {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '!';
            }

            const dialogBox = modal.querySelector('div');
            modal.classList.remove('hidden', 'animate-backdrop-leave');
            modal.classList.add('flex', 'animate-modal-backdrop');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-leave');
                dialogBox.classList.add('animate-modal-enter');
            }

            setTimeout(() => {
                const focusTarget = modal.querySelector('button');
                focusTarget?.focus();
            }, 50);
        }

        function closeAlertModal() {
            const modal = document.getElementById('alertModal');
            if (!modal || modal.classList.contains('hidden')) return;

            const openSidebar = document.getElementById('mobileSidebar');
            if (!openSidebar || openSidebar.classList.contains('hidden')) {
                document.body.classList.remove('overflow-hidden');
            }
            const dialogBox = modal.querySelector('div');
            modal.classList.remove('animate-modal-backdrop');
            modal.classList.add('animate-backdrop-leave');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-enter');
                dialogBox.classList.add('animate-modal-leave');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'animate-backdrop-leave');
                if (dialogBox) dialogBox.classList.remove('animate-modal-leave');
                if (alertModalTriggerEl && typeof alertModalTriggerEl.focus === 'function') {
                    alertModalTriggerEl.focus();
                    alertModalTriggerEl = null;
                }
            }, 150);
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-xl shadow-toast border flex items-start gap-3 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-800 animate-toast-in transition backdrop-blur-md`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

            let iconSymbol = '!';
            let badgeBg = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700';

            if (type === 'success') {
                iconSymbol = '✓';
                badgeBg = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
            } else if (type === 'error') {
                iconSymbol = '✕';
                badgeBg = 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800';
            } else if (type === 'warning') {
                iconSymbol = '▲';
                badgeBg = 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800';
            }

            toast.innerHTML = `
                <div class="w-7 h-7 rounded-lg ${badgeBg} border flex items-center justify-center font-mono text-xs font-bold shrink-0" aria-hidden="true">
                    ${iconSymbol}
                </div>
                <div class="flex-1 min-w-0 font-sans">
                    <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">${title}</div>
                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs p-1 font-mono transition" aria-label="Tutup notifikasi">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                dismissToast(toast);
            }, 5000);
        }

        function dismissToast(element) {
            if (!element || element.dataset.dismissing === 'true') return;
            element.dataset.dismissing = 'true';
            element.classList.remove('animate-toast-in');
            element.classList.add('animate-toast-out');
            setTimeout(() => {
                element.remove();
            }, 150);
        }

        // Global Fetch API Error Interceptor for Popups
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            try {
                const response = await originalFetch(...args);
                if (!response.ok) {
                    const clonedRes = response.clone();
                    try {
                        const errorData = await clonedRes.json();
                        const msg = errorData.message || errorData.error || 'Terjadi kesalahan HTTP ' + response.status;
                        showToast('error', `Gagal (${response.status})`, msg);
                    } catch (e) {
                        showToast('error', `Gagal (${response.status})`, 'Permintaan server mengalami kesalahan.');
                    }
                }
                return response;
            } catch (err) {
                showToast('error', 'Koneksi Terputus', 'Gagal terhubung ke server. Periksa jaringan Anda.');
                throw err;
            }
        };

        // Double-posting prevention with loading state & HTML5 constraint validation checks
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
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" role="status" aria-label="Memproses">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses...</span>
                `;

                let isNavigating = false;
                const onBeforeUnload = () => { isNavigating = true; };
                window.addEventListener('beforeunload', onBeforeUnload, { once: true });

                // Safe timeout recovery: only re-enable if NOT navigating away
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

        // Theme Switcher Functions
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
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark: !isDark } }));
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

        // Instant Link Click Page Navigation Transition & Top Progress Bar
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            // Completely prevent interaction and fragment mutation on disabled or placeholder links
            if (link.getAttribute('aria-disabled') === 'true' || link.classList.contains('pointer-events-none') || !link.hasAttribute('href')) {
                if (!link.hasAttribute('href') || link.getAttribute('aria-disabled') === 'true') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }

            const href = link.getAttribute('href');
            const target = link.getAttribute('target');

            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey) {
                return;
            }

            if (href.startsWith('/') || href.startsWith(window.location.origin)) {
                const bar = document.getElementById('topProgressBar');
                if (bar) {
                    bar.style.transition = 'none';
                    bar.style.transform = 'translateX(-100%)';
                    bar.style.opacity = '1';
                    requestAnimationFrame(() => {
                        bar.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
                        bar.style.transform = 'translateX(-20%)';
                    });
                }

                const mainEl = document.querySelector('main');
                if (mainEl) {
                    mainEl.classList.add('page-exit-active');
                }
            }
        });

        window.addEventListener('pageshow', function() {
            const bar = document.getElementById('topProgressBar');
            if (bar) {
                bar.style.transition = 'transform 0.2s ease-out, opacity 0.2s ease-out';
                bar.style.transform = 'translateX(0%)';
                setTimeout(() => {
                    bar.style.opacity = '0';
                }, 200);
            }
            // Restore any buttons if page was restored from back/forward cache or navigation cancelled
            document.querySelectorAll('button[type="submit"][aria-busy="true"]').forEach(btn => {
                if (btn.dataset.originalHtml) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.removeAttribute('aria-busy');
                    btn.innerHTML = btn.dataset.originalHtml;
                    delete btn.dataset.originalHtml;
                }
            });
            const mainEl = document.querySelector('main');
            if (mainEl) {
                mainEl.classList.remove('page-exit-active');
            }
        });

        // Flash session toasts & theme state init
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcons();

            @if(session('success'))
                showToast('success', 'Berhasil Ditindaklanjuti', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showToast('error', 'Operasi Gagal', '{{ session('error') }}');
            @endif

            @if(session('info'))
                showToast('info', 'Informasi Sistem', '{{ session('info') }}');
            @endif

            @if(isset($errors) && $errors->any())
                const errorMessages = @json($errors->all());
                showToast('error', 'Validasi Form Gagal', errorMessages[0]);
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
