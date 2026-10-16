<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>{{ $verification ? 'Verifikasi Ijazah Resmi - ' . $verification['nama_mahasiswa'] : 'Verifikasi Ijazah Tidak Ditemukan' }} - SIAKAD Enterprise</title>
    <!-- Preconnect to Google Fonts for Fast Non-Blocking Delivery -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <!-- Assets: Vite Compiled Assets with Prototyping CDN Fallback -->
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
                            zinc: {
                                950: '#09090b',
                            }
                        }
                    }
                }
            }
        </script>
    @endif
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #09090b; color: #fafafa; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; color: #000000 !important; }
            .print-card { border: 1px solid #ccc !important; background: #ffffff !important; color: #000000 !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-3 sm:p-6 bg-zinc-950 text-zinc-100 antialiased">

    <!-- Top Navigation Bar -->
    <div class="w-full max-w-3xl flex justify-between items-center py-3 px-4 mb-4 text-white border-b border-zinc-800 no-print">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center font-bold text-white text-xs font-mono">
                SIVIL
            </div>
            <div>
                <span class="font-bold text-sm tracking-tight block text-white">SIAKAD &bull; SIVIL VERIFIKASI</span>
                <span class="text-[10px] text-zinc-400 font-mono block">Layanan Verifikasi Ijazah Nasional Elektronik</span>
            </div>
        </div>
        <div class="flex items-center gap-2.5 text-xs font-mono">
            <a href="{{ route('cek.index') }}" class="bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 px-3 py-1.5 rounded-lg text-zinc-300 transition">
                Portal Mandiri
            </a>
            <a href="{{ route('login') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold px-3.5 py-1.5 rounded-lg transition">
                Login Staff &rarr;
            </a>
        </div>
    </div>

    <!-- Main Verification Card -->
    <div class="w-full max-w-3xl bg-zinc-900 rounded-xl overflow-hidden border border-zinc-800 mb-8 shadow-2xl print-card">
        
        <!-- Header Banner -->
        <div class="bg-zinc-950 border-b border-zinc-800 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-800 text-[11px] font-mono font-semibold uppercase tracking-wider text-zinc-300 mb-3">
                <span>REPUBLIK INDONESIA</span>
                <span>&bull;</span>
                <span>SISTEM SIVIL DIKTI</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                Verifikasi Keaslian Ijazah &amp; Gelar Akademik
            </h1>
            <p class="text-zinc-400 text-xs mt-1.5 max-w-lg mx-auto">
                Sistem Verifikasi Ijazah Elektronik Nasional resmi dengan pengamanan kriptografis internal universitas dan kesiapan integrasi CA Nasional (BSrE BSSN).
            </p>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            @if($verification)
                <!-- Verification Status Callout -->
                <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40 flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center font-bold text-base shrink-0 font-mono">
                        ✓
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-emerald-300">
                            DOKUMEN TERVERIFIKASI SAH &amp; VALID
                        </h2>
                        <p class="text-xs text-emerald-200/80 mt-0.5 leading-relaxed">
                            Ijazah ini resmi diterbitkan oleh Universitas SIAKAD Enterprise dan terdata sah dalam pangkalan data nasional dengan nomor penomoran ijazah nasional (PIN).
                        </p>
                    </div>
                </div>

                <!-- Verification Levels Matrix -->
                @if(isset($verification['tingkat_verifikasi']))
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] font-mono">
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span class="text-zinc-300">Token Terdaftar</span>
                    </div>
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span class="text-zinc-300">Pangkalan Data Sah</span>
                    </div>
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span class="text-zinc-300">Integritas Hash</span>
                    </div>
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span class="text-zinc-300">HMAC-SHA256 Sah</span>
                    </div>
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span class="text-zinc-300">Tidak Dicabut</span>
                    </div>
                    <div class="p-2.5 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center gap-2">
                        <span class="text-blue-400 font-bold">ℹ</span>
                        <span class="text-zinc-300">Gateway BSrE Ready</span>
                    </div>
                </div>
                @endif

                <!-- Detailed Verification Record Table -->
                <div class="bg-zinc-950 rounded-xl border border-zinc-800 overflow-hidden">
                    <div class="p-3.5 border-b border-zinc-800 bg-zinc-900/60 flex justify-between items-center font-mono">
                        <span class="text-xs font-bold text-zinc-200 uppercase tracking-wider">Lembar Data Verifikasi Kelulusan</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold">
                            {{ $verification['status_kelulusan'] }}
                        </span>
                    </div>

                    <div class="divide-y divide-zinc-800/80 text-xs font-mono">
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Nomor Ijazah Nasional (PIN):</span>
                            <span class="font-bold text-emerald-400 text-sm tracking-wide">{{ $verification['nomor_ijazah'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1 font-sans">
                            <span class="text-zinc-400 font-mono">Nama Lengkap Lulusan:</span>
                            <span class="font-bold text-zinc-100 text-sm">{{ $verification['nama_mahasiswa'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Nomor Induk Mahasiswa (NIM):</span>
                            <span class="font-bold text-zinc-200">{{ $verification['nim'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Perguruan Tinggi:</span>
                            <span class="font-semibold text-zinc-200">{{ $verification['universitas'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Program Studi / Jenjang:</span>
                            <span class="font-semibold text-zinc-200">{{ $verification['program_studi'] }} ({{ $verification['jenjang_pendidikan'] }})</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Tanggal Kelulusan / Yudisium:</span>
                            <span class="font-semibold text-zinc-200">{{ $verification['tanggal_kelulusan'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-1">
                            <span class="text-zinc-400">Otoritas Sertifikasi Digital:</span>
                            <span class="text-zinc-300">{{ $verification['penerbit_sertifikat'] }}</span>
                        </div>
                        <div class="p-3.5 flex flex-col gap-1.5">
                            <span class="text-zinc-400">Tanda Tangan Elektronik (Digest Hash):</span>
                            <div class="p-2 bg-zinc-900 border border-zinc-800 rounded font-mono text-[10px] text-zinc-400 break-all select-all">
                                {{ $verification['digital_signature_hash'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UU PDP Privacy Compliance Note -->
                <div class="text-[11px] text-zinc-400 bg-zinc-950 p-4 rounded-xl border border-zinc-800/80 leading-relaxed space-y-1">
                    <div class="font-bold text-zinc-300 font-mono uppercase tracking-wider text-[10px]">
                        Kebijakan Kerahasiaan &bull; UU PDP No. 27/2022
                    </div>
                    <p>
                        Informasi di atas merupakan catatan resmi publik yang diizinkan untuk verifikasi keabsahan ijazah oleh instansi/perusahaan. Sesuai Undang-Undang Pelindungan Data Pribadi (UU PDP), rincian nomor identitas kependudukan (NIK), kontak pribadi, nama orang tua, dan nilai transkrip tidak dipublikasikan ke portal publik.
                    </p>
                </div>

                <div class="flex justify-between items-center gap-3 no-print pt-2">
                    <span class="text-[10px] text-zinc-500 font-mono">Diverifikasi: {{ $verification['verified_at'] }}</span>
                    <button onclick="window.print()" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-lg text-xs font-semibold font-mono transition">
                        Cetak Bukti Verifikasi &bull; Print
                    </button>
                </div>

            @else
                <!-- Invalid / Not Found State -->
                <div class="p-6 rounded-xl bg-rose-950/30 border border-rose-500/40 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center font-bold text-xl mx-auto font-mono">
                        ✕
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-rose-300">
                            DATA DOKUMEN TIDAK DITEMUKAN
                        </h2>
                        <p class="text-xs text-rose-200/80 mt-1 max-w-md mx-auto leading-relaxed">
                            {{ $error ?? 'Ijazah dengan token verifikasi tersebut tidak terdaftar atau belum diterbitkan dalam sistem akademik resmi.' }}
                        </p>
                    </div>
                    <div class="text-[11px] font-mono text-zinc-400 pt-2 border-t border-rose-900/40">
                        Token yang dicari: <span class="text-zinc-200 select-all">{{ $token }}</span>
                    </div>
                </div>

                <div class="text-center pt-2 no-print">
                    <a href="{{ route('cek.index') }}" class="inline-flex items-center gap-1 text-xs text-zinc-400 hover:text-zinc-200 transition font-mono">
                        &larr; Kembali ke Portal Layanan Mandiri
                    </a>
                </div>
            @endif
        </div>

        <!-- Card Footer -->
        <div class="bg-zinc-950 p-4 text-center text-zinc-500 text-xs border-t border-zinc-800 flex justify-between items-center font-mono">
            <span>&copy; {{ date('Y') }} SIAKAD Enterprise &bull; SIVIL Kemendikbudristek</span>
            <span>Otoritas Akademik Universitas &bull; Gateway BSrE Ready</span>
        </div>
    </div>

</body>
</html>
