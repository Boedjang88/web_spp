<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive API Documentation & Console - Web SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        pre, code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-slate-800/80 backdrop-blur border-b border-slate-700 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-md">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="font-bold text-base text-white block">Web SPP RESTful API Docs</span>
                    <span class="text-xs text-slate-400">Laravel Sanctum Protected & Interactive Tester</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-2 rounded-lg font-semibold transition">
                    &larr; Web Dashboard
                </a>
                <button onclick="quickLogin()" class="text-xs bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded-lg font-semibold transition shadow-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg> Auto-Login & Set Token
                </button>
            </div>
        </div>
    </header>

    <!-- Token Banner -->
    <div class="bg-slate-800 border-b border-slate-700 py-3 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-slate-400">Bearer Token:</span>
                <input type="text" id="activeToken" placeholder="Klik 'Auto-Login' atau tempel Sanctum token..."
                    class="bg-slate-900 border border-slate-700 rounded px-3 py-1 text-slate-200 font-mono text-[11px] w-72 sm:w-96 focus:outline-none focus:border-blue-500">
            </div>
            <div class="text-slate-400">
                Base URL: <code class="bg-slate-900 px-2 py-0.5 rounded text-blue-400 font-mono">{{ url('/api') }}</code>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Endpoint Navigation & Details (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 0. Public Portal (No Auth) -->
            <div class="bg-slate-800 rounded-2xl border border-blue-500/30 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-blue-400 uppercase tracking-wider">0. Portal Mandiri Siswa (Public / No Auth)</h3>
                    <span class="text-[10px] uppercase font-bold bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded">Publik</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/portal/siswa/{nisn}</code>
                            <p class="text-[11px] text-slate-400 mt-1">Cek tagihan, status lunas/tunggakan, & riwayat kwitansi via NISN</p>
                        </div>
                        <button onclick="testApi('GET', '/api/portal/siswa/0051234567', null, false)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 1. Authentication & Profile -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-blue-400 uppercase tracking-wider">1. Autentikasi &amp; Profil Mandiri (Sanctum)</h3>
                <div class="space-y-2 text-xs">
                    
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold font-mono mr-2">POST</span>
                            <code class="text-slate-200">/api/auth/login</code>
                            <p class="text-[11px] text-slate-400 mt-1">Login pengguna &amp; dapatkan Sanctum Bearer Token</p>
                        </div>
                        <button onclick="testApi('POST', '/api/auth/login', {'email':'admin@siakad.ac.id', 'password':'password123'}, false)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/auth/me</code>
                            <p class="text-[11px] text-slate-400 mt-1">Profil user yang sedang aktif beserta data role &amp; relasi</p>
                        </div>
                        <button onclick="testApi('GET', '/api/auth/me', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 font-bold font-mono mr-2">PUT</span>
                            <code class="text-slate-200">/api/auth/profile</code>
                            <p class="text-[11px] text-slate-400 mt-1">Perbarui nama dan email login akun mandiri</p>
                        </div>
                        <button onclick="testApi('PUT', '/api/auth/profile', {'name':'Admin TU Update','email':'admin@siakad.ac.id'}, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 font-bold font-mono mr-2">PUT</span>
                            <code class="text-slate-200">/api/auth/change-password</code>
                            <p class="text-[11px] text-slate-400 mt-1">Ubah kata sandi akun (validasi sandi saat ini)</p>
                        </div>
                        <button onclick="testApi('PUT', '/api/auth/change-password', {'current_password':'password123','password':'newpassword123','password_confirmation':'newpassword123'}, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                </div>
            </div>

            <!-- 1.5. User Management (Superadmin & Admin Only) -->
            <div class="bg-slate-800 rounded-2xl border border-purple-500/30 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-purple-400 uppercase tracking-wider">1.5. User Management (RBAC Admin-Only)</h3>
                    <span class="text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded">Admin Only</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/users</code>
                            <p class="text-[11px] text-slate-400 mt-1">Daftar semua pengguna &amp; tingkatan role (Filter: superadmin/admin/guru/siswa)</p>
                        </div>
                        <button onclick="testApi('GET', '/api/users', null, true)" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold font-mono mr-2">POST</span>
                            <code class="text-slate-200">/api/users</code>
                            <p class="text-[11px] text-slate-400 mt-1">Admin membuat akun baru Dosen atau Mahasiswa</p>
                        </div>
                        <button onclick="testApi('POST', '/api/users', {'name':'Dosen Informatika Baru','email':'dosen.informatika@siakad.ac.id','password':'password123','role':'guru','is_active':true}, true)" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 2. Dashboard Analytics & Audit Log -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-blue-400 uppercase tracking-wider">2. Analytics & Audit Trail</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/dashboard/summary</code>
                            <p class="text-[11px] text-slate-400 mt-1">Statistik pemasukan, siswa, kelas, & transaksi</p>
                        </div>
                        <button onclick="testApi('GET', '/api/dashboard/summary', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/activity-logs</code>
                            <p class="text-[11px] text-slate-400 mt-1">Log aktivitas & audit trail keamanan sistem</p>
                        </div>
                        <button onclick="testApi('GET', '/api/activity-logs', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 3. Master Data: Siswa & Tunggakan -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-blue-400 uppercase tracking-wider">3. Siswa & Surat Tagihan</h3>
                <div class="space-y-2 text-xs">
                    
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/siswa</code>
                            <p class="text-[11px] text-slate-400 mt-1">Daftar siswa dengan relasi kelas & tarif SPP</p>
                        </div>
                        <button onclick="testApi('GET', '/api/siswa', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/siswa/1/tunggakan</code>
                            <p class="text-[11px] text-slate-400 mt-1">Kalkulasi daftar bulan nunggak & nominal rupiah</p>
                        </div>
                        <button onclick="testApi('GET', '/api/siswa/1/tunggakan', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/siswa/1/surat-tagihan</code>
                            <p class="text-[11px] text-slate-400 mt-1">Generate format surat tagihan resmi SPP siswa</p>
                        </div>
                        <button onclick="testApi('GET', '/api/siswa/1/surat-tagihan', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-emerald-500/30 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/siswa/1/kartu-ujian</code>
                            <p class="text-[11px] text-slate-400 mt-1">Status kelayakan Kartu Peserta Ujian (Bebas Tanggungan SPP Lock)</p>
                        </div>
                        <button onclick="testApi('GET', '/api/siswa/1/kartu-ujian', null, true)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                </div>
            </div>

            <!-- 4. Transaksi Pembayaran & Kwitansi -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-blue-400 uppercase tracking-wider">4. Transaksi Pembayaran & Kwitansi</h3>
                <div class="space-y-2 text-xs">
                    
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/pembayaran</code>
                            <p class="text-[11px] text-slate-400 mt-1">Riwayat transaksi pembayaran SPP</p>
                        </div>
                        <button onclick="testApi('GET', '/api/pembayaran', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/pembayaran/1/kwitansi</code>
                            <p class="text-[11px] text-slate-400 mt-1">Kwitansi digital resmi (JSON + terbilang rupiah)</p>
                        </div>
                        <button onclick="testApi('GET', '/api/pembayaran/1/kwitansi', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold font-mono mr-2">POST</span>
                            <code class="text-slate-200">/api/pembayaran/batch</code>
                            <p class="text-[11px] text-slate-400 mt-1">Bayar sekaligus banyak bulan (Batch Transaction)</p>
                        </div>
                        <button onclick="testApi('POST', '/api/pembayaran/batch', {'id_siswa':1, 'bulan_list':['November','Desember'], 'tahun_dibayar':2025}, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/laporan/rekap</code>
                            <p class="text-[11px] text-slate-400 mt-1">Rekapitulasi total pemasukan dan transaksi</p>
                        </div>
                        <button onclick="testApi('GET', '/api/laporan/rekap', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>

                </div>
            </div>

            <!-- 5. SIAKAD: Guru & Tenaga Pendidik -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-indigo-400 uppercase tracking-wider">5. SIAKAD: Guru &amp; Tenaga Pendidik</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/guru</code>
                            <p class="text-[11px] text-slate-400 mt-1">Daftar tenaga pengajar, NIP, status kepegawaian, &amp; spesialisasi</p>
                        </div>
                        <button onclick="testApi('GET', '/api/guru', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 6. SIAKAD: Mata Pelajaran & Jadwal -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-indigo-400 uppercase tracking-wider">6. SIAKAD: Mapel &amp; Jadwal Pelajaran</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/mapel</code>
                            <p class="text-[11px] text-slate-400 mt-1">Kurikulum mata pelajaran, KKM standar, &amp; alokasi jam</p>
                        </div>
                        <button onclick="testApi('GET', '/api/mapel', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/jadwal</code>
                            <p class="text-[11px] text-slate-400 mt-1">Jadwal pelajaran kelas terpadu dengan jam &amp; ruangan</p>
                        </div>
                        <button onclick="testApi('GET', '/api/jadwal', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 7. SIAKAD: Penilaian & E-Rapor -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-indigo-400 uppercase tracking-wider">7. SIAKAD: Penilaian &amp; E-Rapor</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/nilai</code>
                            <p class="text-[11px] text-slate-400 mt-1">Data nilai komprehensif (Tugas, UTS, UAS, Nilai Akhir &amp; Predikat)</p>
                        </div>
                        <button onclick="testApi('GET', '/api/nilai', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/nilai/rapor/1</code>
                            <p class="text-[11px] text-slate-400 mt-1">Kalkulasi E-Rapor lengkap beserta IPK rata-rata siswa</p>
                        </div>
                        <button onclick="testApi('GET', '/api/nilai/rapor/1', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

            <!-- 8. SIAKAD: Presensi Siswa -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 space-y-3">
                <h3 class="font-bold text-sm text-indigo-400 uppercase tracking-wider">8. SIAKAD: Presensi Kehadiran</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-bold font-mono mr-2">GET</span>
                            <code class="text-slate-200">/api/presensi</code>
                            <p class="text-[11px] text-slate-400 mt-1">Riwayat presensi harian siswa (Hadir, Izin, Sakit, Alpa)</p>
                        </div>
                        <button onclick="testApi('GET', '/api/presensi', null, true)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-semibold transition">Test</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Live Response Console (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 flex flex-col h-[650px] sticky top-24">
                <div class="flex items-center justify-between border-b border-slate-700 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm text-white">Live Response Console</span>
                        <span id="responseStatus" class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-700 text-slate-300">Ready</span>
                    </div>
                    <button onclick="clearConsole()" class="text-xs text-slate-400 hover:text-white">Clear</button>
                </div>

                <div class="text-xs text-slate-400 mb-2 font-mono flex items-center gap-2">
                    <span id="reqMethod" class="font-bold text-blue-400">-</span>
                    <span id="reqUrl" class="text-slate-300 truncate">-</span>
                </div>

                <pre id="responseJson" class="flex-1 bg-slate-950 p-4 rounded-xl text-xs text-emerald-400 overflow-auto border border-slate-800 leading-relaxed">
// Klik tombol "Test" atau "Auto-Login" di sebelah kiri
// Response JSON akan muncul di sini secara real-time.
                </pre>
            </div>
        </div>

    </div>

    <script>
        let currentToken = '';

        async function quickLogin() {
            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: 'admin@siakad.ac.id', password: 'password123' })
                });
                const data = await res.json();
                if (data.data && data.data.token) {
                    currentToken = data.data.token;
                    document.getElementById('activeToken').value = currentToken;
                    showOutput('POST', '/api/auth/login', res.status, data);
                    alert('<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Login Berhasil! Token Sanctum otomatis aktif.');
                } else {
                    showOutput('POST', '/api/auth/login', res.status, data);
                }
            } catch (err) {
                alert('Error connecting to API: ' + err.message);
            }
        }

        async function testApi(method, endpoint, body = null, needAuth = true) {
            const token = document.getElementById('activeToken').value || currentToken;
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };

            if (needAuth && token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const options = { method, headers };
            if (body && (method === 'POST' || method === 'PUT')) {
                options.body = JSON.stringify(body);
            }

            document.getElementById('reqMethod').innerText = method;
            document.getElementById('reqUrl').innerText = endpoint;
            document.getElementById('responseStatus').innerText = 'Loading...';
            document.getElementById('responseStatus').className = 'text-[11px] font-mono px-2 py-0.5 rounded bg-amber-500/20 text-amber-300';

            try {
                const res = await fetch(endpoint, options);
                const data = await res.json();
                showOutput(method, endpoint, res.status, data);
            } catch (err) {
                document.getElementById('responseStatus').innerText = 'Error';
                document.getElementById('responseStatus').className = 'text-[11px] font-mono px-2 py-0.5 rounded bg-rose-500/20 text-rose-300';
                document.getElementById('responseJson').innerText = err.message;
            }
        }

        function showOutput(method, endpoint, status, data) {
            document.getElementById('reqMethod').innerText = method;
            document.getElementById('reqUrl').innerText = endpoint;
            document.getElementById('responseStatus').innerText = 'HTTP ' + status;

            if (status >= 200 && status < 300) {
                document.getElementById('responseStatus').className = 'text-[11px] font-mono px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold';
            } else {
                document.getElementById('responseStatus').className = 'text-[11px] font-mono px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 font-bold';
            }

            document.getElementById('responseJson').innerText = JSON.stringify(data, null, 2);
        }

        function clearConsole() {
            document.getElementById('reqMethod').innerText = '-';
            document.getElementById('reqUrl').innerText = '-';
            document.getElementById('responseStatus').innerText = 'Ready';
            document.getElementById('responseStatus').className = 'text-[11px] font-mono px-2 py-0.5 rounded bg-slate-700 text-slate-300';
            document.getElementById('responseJson').innerText = '// Console Cleared.';
        }
    </script>
</body>
</html>
