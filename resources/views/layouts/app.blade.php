<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIAKAD & SPP Pro') - SMK Merdeka Belajar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Topbar with Hamburger -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center sticky top-0 z-40 shadow-md">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-sm">🎓</div>
            <span class="font-extrabold text-sm tracking-tight">SIAKAD & SPP</span>
        </div>
        <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')" class="p-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>
    </div>

    <!-- Mobile Drawer Sidebar (Hidden by default on mobile) -->
    <div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm" onclick="this.classList.add('hidden')">
        <div class="w-72 bg-slate-900 h-full p-5 text-slate-200 overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white">🎓</div>
                    <span class="font-bold text-base text-white">SIAKAD Pro</span>
                </div>
                <button onclick="document.getElementById('mobileSidebar').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <!-- Mobile Nav Items (Mirror of Desktop) -->
            <nav class="space-y-1 text-xs">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📊 Dashboard</a>
                <a href="{{ route('web.guru.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">👨‍🏫 Data Guru</a>
                <a href="{{ route('web.mapel.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📚 Mata Pelajaran</a>
                <a href="{{ route('web.jadwal.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📅 Jadwal Pelajaran</a>
                <a href="{{ route('web.nilai.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📝 Nilai &amp; E-Rapor</a>
                <a href="{{ route('web.presensi.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📋 Presensi Siswa</a>
                <a href="{{ route('web.pembayaran.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">💳 Transaksi SPP</a>
                <a href="{{ route('web.siswa.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">👥 Data Siswa</a>
                <a href="{{ route('web.kelas.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">🏫 Data Kelas</a>
                <a href="{{ route('web.spp.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">💰 Tarif SPP</a>
                <a href="{{ route('web.laporan.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">📑 Laporan</a>
                <a href="{{ route('web.activity-logs.index') }}" class="block px-3 py-2 rounded-lg font-medium hover:bg-slate-800 text-slate-300">🛡️ Log Audit</a>
                <a href="{{ url('/api/docs') }}" target="_blank" class="block px-3 py-2 rounded-lg font-medium text-purple-400 hover:bg-purple-900/30">⚡ API Docs</a>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar (Modern Left Panel) -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 h-screen sticky top-0 border-r border-slate-800 z-30 select-none">
        
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-black text-white text-lg shadow-lg shadow-blue-500/20">
                🎓
            </div>
            <div>
                <span class="font-extrabold text-base text-white tracking-tight block">SIAKAD PRO</span>
                <span class="text-[10px] text-blue-400 font-bold uppercase tracking-wider block">SMK Merdeka Belajar</span>
            </div>
        </div>

        <!-- Sidebar Nav Links (Categorized) -->
        <div class="flex-1 px-3 py-4 space-y-5 overflow-y-auto custom-scrollbar text-xs">
            
            <!-- Menu Utama -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Utama</span>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('cek.index') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <span>🔍</span>
                        <span>Portal Mandiri Siswa</span>
                    </a>
                </div>
            </div>

            <!-- Modul Akademik -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Sistem Akademik (SIAKAD)</span>
                <div class="space-y-0.5">
                    <a href="{{ route('web.guru.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.guru.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>👨‍🏫</span>
                        <span>Guru &amp; Pendidik</span>
                    </a>
                    <a href="{{ route('web.mapel.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.mapel.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📚</span>
                        <span>Mata Pelajaran</span>
                    </a>
                    <a href="{{ route('web.jadwal.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.jadwal.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📅</span>
                        <span>Jadwal Pelajaran</span>
                    </a>
                    <a href="{{ route('web.nilai.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.nilai.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📝</span>
                        <span>Nilai &amp; E-Rapor</span>
                    </a>
                    <a href="{{ route('web.presensi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.presensi.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📋</span>
                        <span>Presensi Kehadiran</span>
                    </a>
                </div>
            </div>

            <!-- Modul Keuangan & SPP -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Keuangan &amp; SPP</span>
                <div class="space-y-0.5">
                    <a href="{{ route('web.pembayaran.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.pembayaran.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>💳</span>
                        <span>Transaksi SPP</span>
                    </a>
                    <a href="{{ route('web.siswa.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.siswa.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>👥</span>
                        <span>Data Siswa</span>
                    </a>
                    <a href="{{ route('web.kelas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.kelas.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>🏫</span>
                        <span>Data Kelas</span>
                    </a>
                    <a href="{{ route('web.spp.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.spp.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>💰</span>
                        <span>Tarif SPP</span>
                    </a>
                    <a href="{{ route('web.laporan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.laporan.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>📑</span>
                        <span>Laporan Keuangan</span>
                    </a>
                </div>
            </div>

            <!-- Modul Sistem & Keamanan -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Sistem &amp; Keamanan</span>
                <div class="space-y-0.5">
                    <a href="{{ route('web.activity-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('web.activity-logs.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span>🛡️</span>
                        <span>Log Audit Sistem</span>
                    </a>
                    <a href="{{ url('/api/docs') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-semibold text-purple-400 hover:bg-purple-950/40 hover:text-purple-300 transition">
                        <span>⚡</span>
                        <span>Interactive API Docs</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- User Profile Footer -->
        @auth
        <div class="p-3 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900 border border-slate-800">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 font-black flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-xs text-white block truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-blue-400 font-mono uppercase">{{ auth()->user()->role ?? 'petugas' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/20 hover:text-rose-300 transition" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen">
        
        <!-- Desktop Topbar -->
        <header class="hidden md:flex bg-white border-b border-slate-200 px-6 py-3.5 justify-between items-center sticky top-0 z-20 shadow-sm">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-semibold text-slate-400">Sistem Informasi Akademik &amp; Keuangan Sekolah</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs font-bold text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('cek.index') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition inline-flex items-center gap-1.5">
                    <span>🔍</span> Cek NISN
                </a>
                <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition inline-flex items-center gap-1.5">
                    <span>⚡</span> API Tester
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between text-xs sm:text-sm text-emerald-800 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between text-xs sm:text-sm text-rose-800 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm text-xs text-amber-800">
                    <div class="font-bold mb-1">Terdapat kesalahan input formulir:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-400 mt-auto">
            &copy; {{ date('Y') }} <strong>SIAKAD &amp; SPP Pro</strong> &bull; Sistem Informasi Akademik Sekolah Terpadu berbasis Laravel.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
