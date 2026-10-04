<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIAKAD & SPP') - SMK Merdeka Belajar</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            850: '#151e2e',
                            950: '#0b0f19',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 10px -2px rgba(0, 0, 0, 0.05), 0 1px 4px -1px rgba(0, 0, 0, 0.03)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.04)',
                        'toast': '0 10px 30px -5px rgba(0, 0, 0, 0.15)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

        @keyframes toastSlideIn {
            from { transform: translateX(110%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastFadeOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(110%); opacity: 0; }
        }
        .animate-toast-in { animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-toast-out { animation: toastFadeOut 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Floating Toast Notification Container (Top-Right) -->
    <div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <!-- Mobile Topbar with Hamburger -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center sticky top-0 z-40 shadow-sm border-b border-slate-800">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-sm shadow-sm">🎓</div>
            <div>
                <span class="font-bold text-sm tracking-tight block">SIAKAD &amp; SPP</span>
                <span class="text-[10px] text-slate-400 block -mt-0.5">SMK Merdeka</span>
            </div>
        </div>
        <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>
    </div>

    <!-- Mobile Drawer Sidebar -->
    <div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm" onclick="this.classList.add('hidden')">
        <div class="w-72 bg-slate-900 h-full p-5 text-slate-200 overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white shadow-sm">🎓</div>
                    <span class="font-bold text-sm text-white">SIAKAD Pro</span>
                </div>
                <button onclick="document.getElementById('mobileSidebar').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>
            <nav class="space-y-1 text-xs">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📊 Dashboard</a>
                @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
                    <a href="{{ route('web.users.index') }}" class="block px-3 py-2 rounded-xl font-medium text-indigo-300 hover:bg-slate-800">👥 Manajemen Pengguna</a>
                    <a href="{{ route('web.guru.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">👨‍🏫 Data Guru</a>
                    <a href="{{ route('web.mapel.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📚 Mata Pelajaran</a>
                    <a href="{{ route('web.jadwal.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📅 Jadwal Pelajaran</a>
                    <a href="{{ route('web.nilai.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📝 Nilai &amp; E-Rapor</a>
                    <a href="{{ route('web.presensi.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📋 Presensi Siswa</a>
                    <a href="{{ route('web.pembayaran.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">💳 Transaksi SPP</a>
                    <a href="{{ route('web.siswa.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">👥 Data Siswa</a>
                    <a href="{{ route('web.kelas.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">🏫 Data Kelas</a>
                    <a href="{{ route('web.spp.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">💰 Tarif SPP</a>
                    <a href="{{ route('web.laporan.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📑 Laporan Keuangan</a>
                    <a href="{{ route('web.activity-logs.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">🛡️ Log Audit</a>
                @elseif(auth()->check() && auth()->user()->role === 'guru')
                    <a href="{{ route('web.jadwal.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📅 Jadwal Mengajar</a>
                    <a href="{{ route('web.nilai.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📝 Input Nilai Siswa</a>
                    <a href="{{ route('web.presensi.index') }}" class="block px-3 py-2 rounded-xl font-medium hover:bg-slate-800 text-slate-300">📋 Presensi Kehadiran</a>
                @endif
                <a href="{{ url('/api/docs') }}" target="_blank" class="block px-3 py-2 rounded-xl font-medium text-indigo-400 hover:bg-slate-800">⚡ API Docs</a>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar (Calm Modern Slate Style) -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 h-screen sticky top-0 border-r border-slate-800/80 z-30 select-none">
        
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800/80 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center font-bold text-white shadow-sm">
                🎓
            </div>
            <div>
                <span class="font-bold text-sm text-white tracking-tight block">SIAKAD &amp; SPP</span>
                <span class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wider block">SMK Merdeka</span>
            </div>
        </div>

        <!-- Sidebar Nav Links -->
        <div class="flex-1 px-3 py-4 space-y-5 overflow-y-auto custom-scrollbar text-xs">
            
            <!-- Menu Utama -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Utama</span>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600/90 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('cek.index') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                        <span>🔍</span>
                        <span>Portal Siswa</span>
                    </a>
                </div>
            </div>

            @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin' || auth()->user()->role === 'petugas'))
                <!-- Modul Manajemen Pengguna -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Pengguna</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.users.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>👥</span>
                            <span>Manajemen User</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Akademik -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Akademik (SIAKAD)</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.guru.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.guru.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>👨‍🏫</span>
                            <span>Guru &amp; Pendidik</span>
                        </a>
                        <a href="{{ route('web.mapel.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.mapel.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📚</span>
                            <span>Mata Pelajaran</span>
                        </a>
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.jadwal.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📅</span>
                            <span>Jadwal Pelajaran</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.nilai.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📝</span>
                            <span>Nilai &amp; E-Rapor</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.presensi.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📋</span>
                            <span>Presensi Siswa</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Keuangan & SPP -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Keuangan &amp; SPP</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.pembayaran.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.pembayaran.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>💳</span>
                            <span>Transaksi SPP</span>
                        </a>
                        <a href="{{ route('web.siswa.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.siswa.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>👥</span>
                            <span>Data Siswa</span>
                        </a>
                        <a href="{{ route('web.kelas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.kelas.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>🏫</span>
                            <span>Data Kelas</span>
                        </a>
                        <a href="{{ route('web.spp.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.spp.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>💰</span>
                            <span>Tarif SPP</span>
                        </a>
                        <a href="{{ route('web.laporan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.laporan.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📑</span>
                            <span>Laporan Kas</span>
                        </a>
                    </div>
                </div>

                <!-- Modul Sistem & Keamanan -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Sistem</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.activity-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.activity-logs.*') ? 'bg-indigo-600/90 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>🛡️</span>
                            <span>Log Audit</span>
                        </a>
                        <a href="{{ url('/api/docs') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-indigo-400 hover:text-indigo-300 hover:bg-slate-800/60 transition">
                            <span>⚡</span>
                            <span>Interactive API</span>
                        </a>
                    </div>
                </div>
            @elseif(auth()->check() && auth()->user()->role === 'guru')
                <!-- Modul Guru -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Tugas Pengajar</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.jadwal.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📅</span>
                            <span>Jadwal Mengajar</span>
                        </a>
                        <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.nilai.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📝</span>
                            <span>Input Nilai Siswa</span>
                        </a>
                        <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.presensi.*') ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>📋</span>
                            <span>Presensi Siswa</span>
                        </a>
                    </div>
                </div>
            @elseif(auth()->check() && auth()->user()->role === 'siswa')
                <!-- Modul Siswa -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-blue-400 block mb-1">Portal Saya</span>
                    <div class="space-y-0.5">
                        @if(auth()->user()->id_siswa)
                            <a href="{{ route('web.nilai.rapor', auth()->user()->id_siswa) }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                                <span>📄</span>
                                <span>E-Rapor Digital</span>
                            </a>
                            <a href="{{ route('web.siswa.suratTagihan', auth()->user()->id_siswa) }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                                <span>💳</span>
                                <span>Tagihan &amp; Kwitansi</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        <!-- User Profile Footer -->
        @auth
        <div class="p-3 border-t border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-850 border border-slate-800/80">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-xs text-white block truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-400 font-mono uppercase">{{ auth()->user()->role ?? 'petugas' }}</span>
                    </div>
                </div>
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

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen">
        
        <!-- Desktop Topbar (Calm & Minimalist) -->
        <header class="hidden md:flex bg-white border-b border-slate-200/80 px-8 py-3.5 justify-between items-center sticky top-0 z-20 shadow-sm backdrop-blur-md bg-white/95">
            <div class="flex items-center space-x-2 text-xs">
                <span class="font-semibold text-slate-400">Sistem Akademik &amp; Keuangan Terpadu</span>
                <span class="text-slate-300">&bull;</span>
                <span class="font-bold text-slate-600">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="flex items-center space-x-2.5">
                <a href="{{ route('cek.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/70 text-slate-600 text-xs font-semibold transition inline-flex items-center gap-1.5">
                    <span>🔍</span> Cek NISN
                </a>
                <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100/70 text-indigo-700 text-xs font-semibold transition inline-flex items-center gap-1.5">
                    <span>⚡</span> API Console
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto space-y-6">
            @yield('content')
        </main>

        <!-- Calm Minimal Footer -->
        <footer class="bg-white border-t border-slate-200/80 py-4 px-8 text-center text-xs text-slate-400">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>&copy; {{ date('Y') }} SMK Merdeka Belajar &bull; Sistem Informasi Akademik &amp; Keuangan</span>
                <span class="font-mono text-[11px] text-slate-400">Laravel 11 &bull; 4-Tier RBAC</span>
            </div>
        </footer>

    </div>

    <!-- Global Toast & Interactive Feedback System -->
    <script>
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-2xl shadow-toast border flex items-start gap-3 bg-white text-slate-800 animate-toast-in transition`;

            let iconHtml = '✨';
            let badgeBg = 'bg-slate-100 text-slate-700 border-slate-200';

            if (type === 'success') {
                iconHtml = '✅';
                badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } else if (type === 'error') {
                iconHtml = '❌';
                badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
            } else if (type === 'warning') {
                iconHtml = '⚠️';
                badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
            } else if (type === 'info') {
                iconHtml = 'ℹ️';
                badgeBg = 'bg-indigo-50 text-indigo-700 border-indigo-200';
            }

            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl ${badgeBg} border flex items-center justify-center text-sm flex-shrink-0">
                    ${iconHtml}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs text-slate-900">${title}</div>
                    <div class="text-[11px] text-slate-500 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-slate-400 hover:text-slate-600 text-xs p-1 rounded-lg">&times;</button>
            `;

            container.appendChild(toast);

            // Auto dismiss after 4.5 seconds
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
            }, 350);
        }

        // Automatic button loading feedback on form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
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

        // Trigger session flash toasts on load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', 'Status: Berhasil Berjalan', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showToast('error', 'Status: Terjadi Kesalahan', '{{ session('error') }}');
            @endif

            @if(session('info'))
                showToast('info', 'Status: Informasi Sistem', '{{ session('info') }}');
            @endif

            @if($errors->any())
                showToast('error', 'Status: Validasi Gagal', '{{ $errors->first() }}');
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
