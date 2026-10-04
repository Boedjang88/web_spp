<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD Enterprise - Portal Sistem Informasi Akademik & Keuangan Perguruan Tinggi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">

    <!-- Background Glow Effects -->
    <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/30 border border-indigo-400/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <div>
                <span class="font-black text-lg text-white tracking-tight block">SIAKAD Enterprise</span>
                <span class="text-[10px] text-slate-400 block -mt-1 uppercase tracking-widest font-bold">University Portal</span>
            </div>
        </div>

        <nav class="flex items-center gap-3">
            <a href="{{ route('cek.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition border border-transparent">
                Cek Tagihan UKT Publik
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <span>Buka Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <span>Masuk Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endauth
        </nav>
    </header>

    <!-- Hero Content -->
    <main class="w-full max-w-7xl mx-auto px-6 py-12 md:py-20 my-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7 space-y-6 text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                Sistem Informasi Akademik & Keuangan Perguruan Tinggi v3.2
            </div>
            
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                Tata Kelola Perguruan Tinggi <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-blue-400 to-emerald-400">Terpadu & Modern</span>
            </h1>

            <p class="text-slate-400 text-sm md:text-base max-w-2xl leading-relaxed">
                Platform terintegrasi untuk Mahasiswa, Dosen, dan BAAK. Kelola KRS Concurrency, E-KHS, Presensi Geo-Fenced GPS, Pembayaran UKT Gateway, serta Manajemen LMS Kelas Kuliah secara aman dan handal.
            </p>

            <div class="pt-4 flex flex-wrap items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl shadow-xl shadow-indigo-600/25 transition inline-flex items-center gap-2">
                        <span>Masuk ke Dashboard Akademik</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl shadow-xl shadow-indigo-600/25 transition inline-flex items-center gap-2">
                        <span>Masuk Portal Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endauth
                <a href="{{ route('cek.index') }}" class="px-6 py-3.5 bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-semibold text-sm rounded-xl backdrop-blur transition inline-flex items-center gap-2">
                    <span>Cek Status Tagihan UKT</span>
                </a>
            </div>

            <!-- Feature Pills -->
            <div class="pt-6 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-slate-400 text-xs font-semibold">
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
                    <span>UKT Bank Gateway</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="bg-slate-900/80 backdrop-blur border border-slate-800 p-6 md:p-8 rounded-3xl shadow-2xl space-y-5">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Akses Cepat Multi-Portal
                </h3>

                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal Mahasiswa</span>
                            <span class="text-[11px] text-slate-400 block">KRS, Jadwal Kuliah, E-KHS, Tugas & Presensi GPS</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 text-[10px] font-bold border border-indigo-500/20">Mahasiswa</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal Dosen Pengajar</span>
                            <span class="text-[11px] text-slate-400 block">Presensi Sesi Kuliah, Distribusi Tugas & Input Nilai</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">Dosen</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Portal BAAK & Keuangan</span>
                            <span class="text-[11px] text-slate-400 block">Manajemen Matakuliah, Tagihan UKT & Audit EWS</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">BAAK / Admin</span>
                    </div>
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('login') }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition inline-block">
                        Masuk Menggunakan Akun Institusi
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-900 py-6 text-center text-slate-500 text-xs relative z-10">
        &copy; {{ date('Y') }} SIAKAD Enterprise &bull; System Architecture Powered by Laravel 11 &amp; Filament PHP
    </footer>

</body>
</html>
