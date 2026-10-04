<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'SIAKAD Enterprise') - Sistem Informasi Akademik &amp; Keuangan</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            750: '#26334d',
                            850: '#111827',
                            900: '#0f172a',
                            950: '#070b14',
                        },
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    boxShadow: {
                        'soft': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
                        'card': '0 4px 16px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
                        'floating': '0 20px 40px -8px rgba(15, 23, 42, 0.25), 0 6px 16px -4px rgba(15, 23, 42, 0.12)',
                        'toast': '0 12px 32px -4px rgba(15, 23, 42, 0.16)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

        @keyframes toastSlideIn {
            from { transform: translateY(-16px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes toastFadeOut {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-16px); opacity: 0; }
        }
        .animate-toast-in { animation: toastSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-toast-out { animation: toastFadeOut 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col md:flex-row select-text">

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
    <div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300" onclick="toggleMobileMenu()">
        <div class="w-72 max-w-[85vw] bg-slate-950 h-full p-5 text-slate-200 overflow-y-auto flex flex-col justify-between shadow-2xl border-r border-slate-800" onclick="event.stopPropagation()">
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
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Akademik &amp; Master</div>
                        <a href="{{ route('web.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Manajemen User</span>
                        </a>
                        <a href="{{ route('web.guru.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Dosen &amp; Pendidik</span>
                        </a>
                        <a href="{{ route('web.siswa.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Data Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.mapel.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Mata Kuliah</span>
                        </a>
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Perkuliahan</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Nilai &amp; Transkrip</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Mahasiswa</span>
                        </a>

                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Keuangan &amp; UKT</div>
                        <a href="{{ route('web.pembayaran.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Transaksi Pembayaran</span>
                        </a>
                        <a href="{{ route('web.laporan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Laporan Keuangan</span>
                        </a>
                    @elseif(auth()->check() && (auth()->user()->role === 'guru' || auth()->user()->role === 'dosen'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400">Portal Dosen</div>
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Mengajar</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Input Nilai Mahasiswa</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Presensi Kelas</span>
                        </a>
                    @elseif(auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa'))
                        <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-brand-300">Portal Mahasiswa</div>
                        <a href="{{ route('siakad.krs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Smart KRS</span>
                        </a>
                        <a href="{{ route('siakad.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Presensi Kuliah</span>
                        </a>
                        <a href="{{ route('siakad.tugas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Tugas &amp; LMS</span>
                        </a>
                        <a href="{{ route('siakad.ukt.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Pembayaran UKT</span>
                        </a>
                        <a href="{{ route('siakad.biodata.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Lengkapi Biodata</span>
                        </a>
                        <a href="{{ route('siakad.analytics.performance') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-900">
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
        <header class="hidden md:flex bg-white/90 border-b border-slate-200/80 px-8 py-3.5 justify-between items-center sticky top-0 z-20 shadow-soft backdrop-blur-md">
            <div class="flex items-center space-x-3 text-xs">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-50 text-brand-700 border border-brand-200">Production</span>
                <span class="font-semibold text-slate-500">SIAKAD Enterprise &bull; {{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('cek.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-slate-200/60">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Publik</span>
                </a>
                <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100/80 text-brand-700 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-brand-200/60">
                    <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>API Console</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-6 md:p-8 max-w-7xl w-full mx-auto space-y-6">
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

    <!-- Global Toast & Interactive Feedback System -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-2xl shadow-toast border flex items-start gap-3 bg-white text-slate-900 animate-toast-in transition backdrop-blur-md bg-white/98`;

            let iconHtml = '<svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            let badgeBg = 'bg-slate-100 text-slate-700 border-slate-200';

            if (type === 'success') {
                iconHtml = '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } else if (type === 'error') {
                iconHtml = '<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
            } else if (type === 'warning') {
                iconHtml = '<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
            } else if (type === 'info') {
                iconHtml = '<svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                badgeBg = 'bg-brand-50 text-brand-700 border-brand-200';
            }

            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl ${badgeBg} border flex items-center justify-center text-sm flex-shrink-0">
                    ${iconHtml}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs text-slate-900">${title}</div>
                    <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-slate-400 hover:text-slate-600 text-xs p-1 rounded-lg" aria-label="Tutup">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                dismissToast(toast);
            }, 4500);
        }

        function dismissToast(element) {
            if (!element) return;
            element.classList.remove('animate-toast-in');
            element.classList.add('animate-toast-out');
            setTimeout(() => {
                element.remove();
            }, 250);
        }

        // Double-posting prevention with loading state
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses...</span>
                `;
            }
        });

        // Flash session toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', 'Berhasil', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showToast('error', 'Perhatian', '{{ session('error') }}');
            @endif

            @if(session('info'))
                showToast('info', 'Informasi', '{{ session('info') }}');
            @endif

            @if($errors->any())
                showToast('error', 'Validasi Gagal', '{{ $errors->first() }}');
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
