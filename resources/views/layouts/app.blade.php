<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Pembayaran SPP') - Web SPP</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            💳
                        </div>
                        <div>
                            <span class="font-bold text-lg text-slate-900 leading-tight block">Web SPP</span>
                            <span class="text-xs text-slate-500 font-medium block">Sistem Pembayaran SPP</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('web.pembayaran.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.pembayaran.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Transaksi SPP
                    </a>
                    <a href="{{ route('web.siswa.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.siswa.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Data Siswa
                    </a>
                    <a href="{{ route('web.kelas.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.kelas.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Data Kelas
                    </a>
                    <a href="{{ route('web.spp.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.spp.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Tarif SPP
                    </a>
                    <a href="{{ route('web.laporan.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.laporan.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Laporan
                    </a>
                    <a href="{{ route('web.activity-logs.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('web.activity-logs.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Log Audit
                    </a>
                    <a href="{{ url('/api/docs') }}" target="_blank" class="px-3 py-2 rounded-md text-sm font-semibold transition text-purple-600 hover:text-purple-700 hover:bg-purple-50 flex items-center gap-1">
                        <span>⚡</span> API Docs
                    </a>
                </nav>

                <!-- User Profile & Logout -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('cek.index') }}" target="_blank" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1.5 rounded-md font-medium transition hidden sm:inline-flex items-center gap-1">
                        🔍 Cek Tagihan Publik
                    </a>

                    @auth
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <span class="text-xs font-semibold text-slate-800 block">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 inline-block">{{ auth()->user()->role ?? 'petugas' }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 p-2 rounded-lg text-xs font-semibold transition flex items-center gap-1" title="Logout">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2 text-emerald-800 text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2 text-rose-800 text-sm font-medium">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
                <div class="text-amber-800 text-sm font-semibold mb-1">Terdapat beberapa kesalahan pengisian form:</div>
                <ul class="list-disc list-inside text-xs text-amber-700 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} <strong>Web SPP</strong> - Aplikasi Pembayaran SPP Sekolah Berbasis Laravel.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
