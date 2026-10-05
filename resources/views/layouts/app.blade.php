<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>@yield('title', 'SIAKAD Enterprise') - Sistem Informasi Akademik &amp; Keuangan</title>
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#09090b">
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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            750: '#1f2937',
                            850: '#111827',
                            900: '#09090b',
                            950: '#040405',
                        },
                        brand: {
                            50: '#fafafa',
                            100: '#f4f4f5',
                            200: '#e4e4e7',
                            300: '#d4d4d8',
                            400: '#a1a1aa',
                            500: '#71717a',
                            600: '#18181b',
                            700: '#09090b',
                            800: '#040405',
                            900: '#000000',
                        }
                    },
                    boxShadow: {
                        'soft': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.1)',
                        'floating': '0 10px 25px -5px rgba(0, 0, 0, 0.2)',
                        'toast': '0 4px 12px rgba(0, 0, 0, 0.15)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        html.dark body {
            background-color: #09090b !important;
            color: #fafafa !important;
        }
        html:not(.dark) body {
            background-color: #fafafa;
            color: #09090b;
        }

        /* Ultra-Clean Monochrome Enterprise Dark Mode Overrides */
        html.dark .bg-white {
            background-color: #18181b !important;
            color: #fafafa !important;
        }
        html.dark .bg-slate-50,
        html.dark .bg-slate-50\/50,
        html.dark .bg-slate-50\/60,
        html.dark .bg-slate-100 {
            background-color: #27272a !important;
            color: #e4e4e7 !important;
        }
        html.dark .bg-slate-900,
        html.dark .bg-slate-950 {
            background-color: #09090b !important;
            border-color: #27272a !important;
        }
        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-slate-700 {
            color: #fafafa !important;
        }
        html.dark .text-slate-600,
        html.dark .text-slate-500 {
            color: #a1a1aa !important;
        }
        html.dark .text-slate-400 {
            color: #d4d4d8 !important;
        }
        html.dark .border-slate-200,
        html.dark .border-slate-200\/80,
        html.dark .border-slate-200\/60,
        html.dark .border-slate-100,
        html.dark .border-slate-800 {
            border-color: #27272a !important;
        }
        html.dark input[type="text"],
        html.dark input[type="email"],
        html.dark input[type="password"],
        html.dark input[type="number"],
        html.dark input[type="date"],
        html.dark select,
        html.dark textarea {
            background-color: #18181b !important;
            color: #fafafa !important;
            border-color: #27272a !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #71717a !important;
        }
        html.dark table thead tr {
            background-color: #18181b !important;
            color: #a1a1aa !important;
            border-color: #27272a !important;
        }
        html.dark table tbody tr {
            border-color: #27272a !important;
        }
        html.dark table tbody tr:hover {
            background-color: #27272a !important;
        }
        html.dark header {
            background-color: #09090b !important;
            border-color: #27272a !important;
        }
        html.dark footer {
            background-color: #09090b !important;
            border-color: #27272a !important;
            color: #71717a !important;
        }
        html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) {
            border-color: #27272a !important;
        }
        html.dark .shadow-soft,
        html.dark .shadow-card {
            box-shadow: none !important;
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
            transform: scale(0.97);
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
                min-height: 42px;
            }
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }
            .overflow-x-auto table {
                min-width: 580px;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col md:flex-row select-text">

    <!-- Top Sleek Page Loading Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-500 via-indigo-500 to-emerald-400 z-[9999] opacity-0 pointer-events-none transition-all duration-300 transform -translate-x-full"></div>

    <!-- Floating Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-6 z-50 flex flex-col gap-2.5 max-w-md w-auto sm:w-96 pointer-events-none"></div>

    <!-- Mobile Header Bar -->
    <header class="md:hidden bg-slate-950 text-white px-4 py-3 flex justify-between items-center sticky top-0 z-40 shadow-sm border-b border-slate-800/80 backdrop-blur-md bg-slate-950/95">
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
            <button type="button" onclick="toggleTheme()" class="w-8 h-8 rounded-xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 flex items-center justify-center transition active:scale-95" title="Ganti Tema">
                <svg class="theme-icon-sun w-4 h-4 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="theme-icon-moon w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            @auth
            <a href="{{ route('profile.index') }}" class="w-8 h-8 rounded-xl bg-slate-900 text-brand-300 border border-slate-800 flex items-center justify-center font-bold text-xs" title="Profil">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>
            @endauth
            <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 active:scale-95 transition" aria-label="Buka Menu">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </header>

    <!-- Mobile Drawer Sidebar Backdrop -->
    <div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-in-out" onclick="toggleMobileMenu(true)">
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
                    <button type="button" onclick="toggleMobileMenu()" class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-slate-900 border border-slate-800" aria-label="Tutup Menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="space-y-1 text-xs">
                    <a href="{{ route('dashboard') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Akademik &amp; Master</div>
                        <a href="{{ route('web.users.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Manajemen User</span>
                        </a>
                        <a href="{{ route('web.guru.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Dosen &amp; Pendidik</span>
                        </a>
                        <a href="{{ route('web.siswa.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Data Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.mapel.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Mata Kuliah</span>
                        </a>
                        <a href="{{ route('web.jadwal.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Perkuliahan</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Nilai &amp; Transkrip</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Mahasiswa</span>
                        </a>

                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Keuangan &amp; UKT</div>
                        <a href="{{ route('web.pembayaran.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Transaksi Pembayaran</span>
                        </a>
                        <a href="{{ route('web.laporan.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Laporan Keuangan</span>
                        </a>
                    @elseif(auth()->check() && (auth()->user()->role === 'guru' || auth()->user()->role === 'dosen'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400">Portal Dosen</div>
                        <a href="{{ route('web.jadwal.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Mengajar</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Input Nilai Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Kelas</span>
                        </a>
                    @elseif(auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-brand-300">Portal Mahasiswa</div>
                        <a href="{{ route('siakad.krs.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Smart KRS</span>
                        </a>
                        <a href="{{ route('siakad.presensi.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Presensi Kuliah</span>
                        </a>
                        <a href="{{ route('siakad.tugas.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Tugas &amp; LMS</span>
                        </a>
                        <a href="{{ route('siakad.ukt.index') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Pembayaran UKT</span>
                        </a>
                        <a href="{{ route('siakad.biodata.edit') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Lengkapi Biodata</span>
                        </a>
                        <a href="{{ route('siakad.analytics.performance') }}" onclick="toggleMobileMenu(true)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Grafik Performa</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Mobile Logout -->
            @auth
            <div class="pt-4 border-t border-slate-800/80">
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
    <aside class="hidden md:flex flex-col w-64 bg-slate-950 text-slate-300 h-screen sticky top-0 border-r border-slate-850 z-30 select-none flex-shrink-0">
        
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-850 flex items-center space-x-3 bg-slate-950">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-md ring-1 ring-white/20 flex-shrink-0">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div class="truncate">
                <span class="font-extrabold text-sm text-white tracking-tight block truncate">SIAKAD Enterprise</span>
                <span class="text-[10px] text-brand-300 font-semibold uppercase tracking-wider block">Universitas &bull; Portal</span>
            </div>
        </div>

        <!-- Sidebar Nav Links -->
        <div class="flex-1 px-3 py-4 space-y-4 overflow-y-auto custom-scrollbar text-xs">
            
            <!-- Menu Utama -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Utama</span>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
                <!-- Modul Manajemen Pengguna -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Pengguna &amp; Hak Akses</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.users.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Manajemen User</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Akademik -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Akademik &amp; Perkuliahan</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.guru.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.guru.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Dosen &amp; Pendidik</span>
                        </a>
                        <a href="{{ route('web.siswa.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.siswa.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Data Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.mapel.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.mapel.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Mata Kuliah</span>
                        </a>
                        <a href="{{ route('web.kelas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.kelas.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Kelas Perkuliahan</span>
                        </a>
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.jadwal.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Perkuliahan</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.nilai.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Nilai &amp; Transkrip</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.presensi.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Mahasiswa</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Keuangan & UKT -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Keuangan &amp; UKT</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.pembayaran.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.pembayaran.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Transaksi Pembayaran</span>
                        </a>
                        <a href="{{ route('web.spp.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.spp.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tarif UKT / SPP</span>
                        </a>
                        <a href="{{ route('web.laporan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.laporan.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Laporan Keuangan</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Sistem & Keamanan -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Sistem &amp; Keamanan</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.activity-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.activity-logs.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Audit Trail Forensik</span>
                        </a>
                        <a href="{{ url('/api/docs') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-brand-300 hover:text-white hover:bg-slate-900 transition">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Interactive API Console</span>
                        </a>
                    </div>
                </div>
            @elseif(auth()->check() && (auth()->user()->role === 'guru' || auth()->user()->role === 'dosen'))
                <!-- Modul Dosen -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Portal Dosen</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.jadwal.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Mengajar</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.nilai.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Input Nilai Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.presensi.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Kelas &amp; BAP</span>
                        </a>
                    </div>
                </div>
            @elseif(auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa'))
                <!-- Modul Mahasiswa -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-brand-300 block mb-1">Layanan Akademik</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('siakad.krs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.krs.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.krs.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Smart KRS</span>
                        </a>
                        <a href="{{ route('siakad.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.presensi.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.presensi.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Presensi Perkuliahan</span>
                        </a>
                        <a href="{{ route('siakad.tugas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.tugas.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.tugas.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Tugas &amp; LMS</span>
                        </a>
                        <a href="{{ route('siakad.ukt.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.ukt.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.ukt.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Pembayaran UKT</span>
                        </a>
                        <a href="{{ route('siakad.biodata.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.biodata.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.biodata.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Lengkapi Biodata &amp; PDP</span>
                        </a>
                        <a href="{{ route('siakad.analytics.performance') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('siakad.analytics.*') ? 'bg-brand-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('siakad.analytics.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Grafik Performa</span>
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <!-- User Profile Footer -->
        @auth
        <div class="p-3 border-t border-slate-850 bg-slate-950">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <a href="{{ route('profile.index') }}" class="flex items-center space-x-2.5 overflow-hidden flex-1 group">
                    <div class="w-7 h-7 rounded-lg bg-brand-500/20 text-brand-300 font-bold flex items-center justify-center text-xs flex-shrink-0 group-hover:bg-brand-500/30 transition">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-xs text-white block truncate group-hover:text-brand-300 transition">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-400 font-mono uppercase">{{ auth()->user()->role ?? 'mahasiswa' }}</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-h-screen pb-24 md:pb-0 overflow-x-hidden">
        
        <!-- Desktop Header Bar -->
        <header class="hidden md:flex bg-white/90 dark:bg-slate-900/90 border-b border-slate-200/80 dark:border-slate-800 px-8 py-3.5 justify-between items-center sticky top-0 z-20 shadow-soft backdrop-blur-md">
            <div class="flex items-center space-x-3 text-xs">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-50 dark:bg-brand-950 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800">Production</span>
                <span class="font-semibold text-slate-500 dark:text-slate-400">SIAKAD Enterprise &bull; {{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Theme Switcher Button -->
                <button type="button" onclick="toggleTheme()" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-slate-200/60 dark:border-slate-700 cursor-pointer select-none" title="Ganti Tema (Terang / Gelap)">
                    <svg class="theme-icon-sun w-3.5 h-3.5 text-amber-500 dark:text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="theme-icon-moon w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span>Tema: <strong class="theme-label-text">Terang</strong></span>
                </button>

                <a href="{{ route('cek.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-slate-200/60 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Publik</span>
                </a>
                <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-brand-50 dark:bg-brand-900/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-brand-200/60 dark:border-brand-800/60">
                    <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>API Console</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-6 md:p-8 max-w-7xl w-full mx-auto space-y-6 animate-card-in">
            @yield('content')
        </main>

        <!-- Desktop Footer -->
        <footer class="bg-white border-t border-slate-200/80 py-4 px-8 text-center text-xs text-slate-400 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>&copy; {{ date('Y') }} SIAKAD Enterprise &bull; Sistem Informasi Akademik &amp; Keuangan Terpadu</span>
                <span class="font-mono text-[11px] text-slate-400">Laravel 11.x &bull; 4-Tier RBAC &bull; UU PDP Compliant</span>
            </div>
        </footer>

    </div>

    <!-- Mobile Bottom App Dock Navigation Bar -->
    <nav class="md:hidden fixed bottom-3 left-3 right-3 z-40 bg-slate-950/90 backdrop-blur-xl border border-slate-800/90 rounded-2xl shadow-floating p-1.5 flex items-center justify-around select-none">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[9px] mt-0.5">Home</span>
        </a>

        @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
            <a href="{{ route('web.pembayaran.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.pembayaran.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="text-[9px] mt-0.5">Kas</span>
            </a>
            <a href="{{ route('web.siswa.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.siswa.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[9px] mt-0.5">Mhs</span>
            </a>
            <a href="{{ route('web.laporan.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.laporan.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[9px] mt-0.5">Laporan</span>
            </a>
        @elseif(auth()->check() && (auth()->user()->role === 'guru' || auth()->user()->role === 'dosen'))
            <a href="{{ route('web.jadwal.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.jadwal.*') ? 'text-emerald-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-[9px] mt-0.5">Jadwal</span>
            </a>
            <a href="{{ route('web.nilai.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.nilai.*') ? 'text-emerald-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span class="text-[9px] mt-0.5">Nilai</span>
            </a>
            <a href="{{ route('web.presensi.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('web.presensi.*') ? 'text-emerald-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="text-[9px] mt-0.5">Presensi</span>
            </a>
        @elseif(auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa'))
            <a href="{{ route('siakad.presensi.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('siakad.presensi.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[9px] mt-0.5">Presensi</span>
            </a>
            <a href="{{ route('siakad.tugas.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('siakad.tugas.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span class="text-[9px] mt-0.5">Tugas</span>
            </a>
            <a href="{{ route('siakad.ukt.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('siakad.ukt.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="text-[9px] mt-0.5">UKT</span>
            </a>
            <a href="{{ route('siakad.krs.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ request()->routeIs('siakad.krs.*') ? 'text-brand-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="text-[9px] mt-0.5">KRS</span>
            </a>
        @endif

        <button type="button" onclick="toggleMobileMenu()" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-slate-400 hover:text-slate-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[9px] mt-0.5">Menu</span>
        </button>
    </nav>

    <!-- Global Modal Alert Dialog -->
    <div id="alertModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 transition-opacity duration-150 animate-modal-backdrop">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl max-w-md w-full p-5 shadow-2xl space-y-4 animate-modal-enter">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div id="modalIconContainer" class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-mono font-bold text-xs shrink-0">
                        !
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Notifikasi Sistem</h3>
                        <span id="modalTypeBadge" class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider">ALERT</span>
                    </div>
                </div>
                <button type="button" onclick="closeAlertModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-sm font-mono transition">&times;</button>
            </div>
            <div id="modalBody" class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-sans border-y border-zinc-100 dark:border-zinc-800 py-3 max-h-60 overflow-y-auto">
                Pesan notifikasi sistem.
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAlertModal()" class="px-4 py-2 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 active:scale-[0.97] rounded-lg text-xs font-semibold font-mono transition-all duration-150">
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') toggleMobileMenu(true);
        });

        function showAlertModal(title, message, type = 'error') {
            const modal = document.getElementById('alertModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalIconContainer = document.getElementById('modalIconContainer');
            const modalTypeBadge = document.getElementById('modalTypeBadge');

            if (!modal) return;

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
            modal.classList.add('animate-modal-backdrop');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-leave');
                dialogBox.classList.add('animate-modal-enter');
            }
        }

        function closeAlertModal() {
            const modal = document.getElementById('alertModal');
            if (!modal || modal.classList.contains('hidden')) return;

            const dialogBox = modal.querySelector('div');
            modal.classList.remove('animate-modal-backdrop');
            modal.classList.add('animate-backdrop-leave');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-enter');
                dialogBox.classList.add('animate-modal-leave');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('animate-backdrop-leave');
                if (dialogBox) dialogBox.classList.remove('animate-modal-leave');
            }, 150);
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-xl shadow-toast border flex items-start gap-3 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-800 animate-toast-in transition backdrop-blur-md`;

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
                <div class="w-7 h-7 rounded-lg ${badgeBg} border flex items-center justify-center font-mono text-xs font-bold shrink-0">
                    ${iconSymbol}
                </div>
                <div class="flex-1 min-w-0 font-sans">
                    <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">${title}</div>
                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs p-1 font-mono transition" aria-label="Tutup">&times;</button>
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

        // Double-posting prevention with loading state
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                const origText = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses...</span>
                `;
                // Re-enable button after 10s timeout if page doesn't reload
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = origText;
                    }
                }, 10000);
            }
        });

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
        });

        // Flash session toasts & theme state init
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcons();

            @if(session('success'))
                showToast('success', 'Berhasil Ditindaklanjuti', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showToast('error', 'Operasi Gagal', '{{ session('error') }}');
                showAlertModal('Operasi Gagal', '{{ session('error') }}', 'error');
            @endif

            @if(session('info'))
                showToast('info', 'Informasi Sistem', '{{ session('info') }}');
            @endif

            @if($errors->any())
                const errorMessages = @json($errors->all());
                const formattedList = errorMessages.map(msg => `&bull; ${msg}`).join('<br>');
                showToast('error', 'Validasi Form Gagal', errorMessages[0]);
                showAlertModal('Gagal Submisi Form', `<div class="space-y-1"><strong>Daftar Kesalahan:</strong><br>${formattedList}</div>`, 'error');
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
