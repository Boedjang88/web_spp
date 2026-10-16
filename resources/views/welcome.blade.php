<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD Enterprise - Portal Sistem Informasi Akademik & Keuangan Perguruan Tinggi</title>
    <!-- Assets: Vite Compiled Assets with Prototyping CDN Fallback -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card-in { animation: cardFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="bg-workspace-bg-dark text-slate-100 min-h-screen flex flex-col justify-between selection:bg-brand-500 selection:text-white relative overflow-x-hidden font-sans">

    <!-- Navigation Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between relative z-10 animate-card-in">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-sm border border-brand-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <div>
                <span class="font-bold text-lg text-white tracking-tight block">SIAKAD Enterprise</span>
                <span class="text-[10px] text-slate-400 block -mt-1 uppercase tracking-widest font-mono font-semibold">Academic Workspace</span>
            </div>
        </div>

        <nav class="flex items-center gap-3">
            <a href="{{ route('web.cek-tagihan.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Cek Tagihan Publik</span>
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-500 text-white shadow-sm transition flex items-center gap-2">
                    <span>Buka Workspace</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-500 text-white shadow-sm transition flex items-center gap-2">
                    <span>Masuk Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endauth
        </nav>
    </header>

    <!-- Hero Content -->
    <main class="w-full max-w-7xl mx-auto px-6 py-12 md:py-20 my-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center animate-card-in">
        <div class="lg:col-span-7 space-y-6 text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-950/60 border border-brand-800 text-brand-300 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-brand-400"></span>
                SIAKAD Academic Workspace &bull; Enterprise SaaS
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight">
                Ruang Kerja Akademik <br><span class="text-brand-400">Terpadu, Cepat &amp; Aman</span>
            </h1>

            <p class="text-slate-400 text-sm md:text-base max-w-2xl leading-relaxed">
                Platform operasional universitas terintegrasi untuk Mahasiswa, Dosen, BAAK, dan Keuangan. Smart KRS berkecepatan tinggi, presensi presisi geo-fencing, portal pembayaran H2H Bank, serta administrasi kurikulum terstandar PDDIKTI.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm transition inline-flex items-center gap-2">
                        <span>Masuk ke Dashboard Akademik</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm transition inline-flex items-center gap-2">
                        <span>Masuk Workspace Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="{{ route('web.cek-tagihan.index') }}" class="px-5 py-3 bg-workspace-surface-dark hover:bg-[#282a2c] text-slate-200 border border-slate-700/80 font-bold text-xs sm:text-sm rounded-xl transition inline-flex items-center gap-2">
                        <span>Cek Tagihan Mahasiswa</span>
                    </a>
                @endauth
            </div>

            <!-- Feature Pills -->
            <div class="pt-6 border-t border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-4 text-slate-400 text-xs font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>High-Concurrency KRS</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Geo-Fenced Presensi</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>UU PDP Encrypted</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Bank H2H Gateway</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="bg-workspace-surface-dark border border-slate-800 p-6 md:p-8 rounded-3xl shadow-2xs space-y-5">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Akses Cepat Multi-Peran
                </h3>

                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-workspace-bg-dark border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal Mahasiswa</span>
                            <span class="text-[11px] text-slate-400 block">KRS, Jadwal Kuliah, E-KHS, &amp; Presensi GPS</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-brand-950/60 text-brand-300 text-[10px] font-bold border border-brand-800">Mahasiswa</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-workspace-bg-dark border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal Dosen Pengajar</span>
                            <span class="text-[11px] text-slate-400 block">Persetujuan KRS, BAP Sesi &amp; Input Nilai</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-950/60 text-emerald-300 text-[10px] font-bold border border-emerald-800">Dosen</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-workspace-bg-dark border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal BAAK &amp; Keuangan</span>
                            <span class="text-[11px] text-slate-400 block">Manajemen Matakuliah, Tagihan UKT &amp; EWS DO</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-[10px] font-bold border border-slate-700">BAAK / Admin</span>
                    </div>
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('login') }}" class="w-full py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition inline-block shadow-sm">
                        Masuk Menggunakan Akun Institusi
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-800 py-6 text-center text-slate-500 text-xs relative z-10">
        &copy; {{ date('Y') }} SIAKAD Enterprise &bull; Academic Workspace Architecture Powered by Laravel 11 &amp; Filament PHP
    </footer>

</body>
</html>
