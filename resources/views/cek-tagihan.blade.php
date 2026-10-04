<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Layanan Mahasiswa - SIAKAD Enterprise</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #09090b; color: #fafafa; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

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
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-3 sm:p-6 bg-zinc-950 text-zinc-100 antialiased">

    <!-- Top Sleek Page Loading Progress Bar -->
    <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-zinc-500 via-indigo-500 to-emerald-400 z-[9999] opacity-0 pointer-events-none transition-all duration-300 transform -translate-x-full"></div>

    <!-- Top Navigation Bar -->
    <div class="w-full max-w-4xl flex justify-between items-center py-3 px-4 mb-4 text-white border-b border-zinc-800">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center font-bold text-white text-xs font-mono">
                SIAKAD
            </div>
            <div>
                <span class="font-bold text-sm tracking-tight block text-white">SIAKAD ENTERPRISE</span>
                <span class="text-[10px] text-zinc-400 font-mono block">Universitas &bull; Portal Layanan Mandiri</span>
            </div>
        </div>
        <div class="flex items-center gap-2.5 text-xs font-mono">
            <a href="{{ url('/api/docs') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1 bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 px-3 py-1.5 rounded-lg text-zinc-300 transition">
                API Docs
            </a>
            <a href="{{ route('login') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold px-3.5 py-1.5 rounded-lg transition">
                Login Portal &rarr;
            </a>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="w-full max-w-4xl bg-zinc-900 rounded-xl overflow-hidden border border-zinc-800 mb-8">
        
        <!-- Header Executive Banner -->
        <div class="bg-zinc-950 border-b border-zinc-800 p-6 md:p-8 text-white">
            <div class="text-center max-w-xl mx-auto">
                <span class="px-2.5 py-0.5 bg-zinc-900 text-zinc-300 border border-zinc-800 rounded text-[10px] font-mono font-semibold uppercase tracking-wider mb-2 inline-block">
                    UNIVERSITAS &bull; SISTER KEMENDIKBUDRISTEK
                </span>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Portal Layanan Mandiri Mahasiswa</h1>
                <p class="text-zinc-400 text-xs mt-1">Masukkan NIM / NISN mahasiswa untuk mengecek E-KHS, nilai hasil studi, dan status pembayaran UKT.</p>
            </div>
        </div>

        <div class="p-5 md:p-8">
            <!-- Search Form -->
            <form action="{{ route('cek.search') }}" method="POST" class="mb-6">
                @csrf
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative w-full">
                        <input type="text" name="nisn" placeholder="Masukkan NIM / NISN Mahasiswa..." required
                            class="w-full px-4 py-3 rounded-lg bg-zinc-950 border border-zinc-700 text-white focus:outline-none focus:border-zinc-500 transition text-sm font-mono"
                            value="{{ request('nisn', $siswa->nisn ?? '') }}">
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold py-3 px-6 rounded-lg transition text-xs whitespace-nowrap font-mono">
                        Periksa Data
                    </button>
                </div>

                @if(session('error'))
                    <div class="mt-4 bg-zinc-950 border border-zinc-800 text-zinc-300 p-3 rounded-lg text-xs font-mono" role="alert">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </form>

            @if(isset($siswa))
                @php 
                    $info = $siswa->info_tunggakan;
                    $kehadiran = $siswa->rekap_kehadiran;
                @endphp

                <div class="space-y-5">
                    
                    <!-- Student Identity Card -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-zinc-950 p-4 rounded-lg border border-zinc-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold uppercase tracking-wider bg-zinc-800 text-zinc-300 border border-zinc-700">Mahasiswa Aktif</span>
                                <span class="text-xs text-zinc-400 font-mono">NIM: {{ $siswa->nis }}</span>
                            </div>
                            <h2 class="text-lg font-bold text-white mt-1">{{ $siswa->nama }}</h2>
                            <div class="text-zinc-400 text-xs mt-1 flex flex-wrap items-center gap-2 font-mono">
                                <span><strong>NISN:</strong> {{ $siswa->nisn }}</span>
                                <span>&bull;</span>
                                <span><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }}</span>
                                <span>&bull;</span>
                                <span><strong>Tarif UKT:</strong> Rp {{ number_format($siswa->spp->nominal, 0, ',', '.') }}/sem</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank"
                                class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1 font-mono">
                                Cetak E-KHS
                            </a>
                            <a href="{{ route('web.siswa.suratTagihan', $siswa->id) }}" target="_blank"
                                class="px-3 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-xs font-semibold border border-zinc-700 transition inline-flex items-center gap-1 font-mono">
                                Surat Billing UKT
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Portal Tabs -->
                    <div class="border-b border-zinc-800">
                        <div class="flex space-x-2 text-xs font-mono" id="portalTabs">
                            <button type="button" onclick="switchTab('tab-spp')" id="btn-tab-spp" class="tab-btn px-3 py-2 border-b-2 border-zinc-100 text-zinc-100 transition">
                                Keuangan &amp; UKT
                            </button>
                            <button type="button" onclick="switchTab('tab-nilai')" id="btn-tab-nilai" class="tab-btn px-3 py-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-300 transition">
                                Transkrip / KHS ({{ $siswa->nilais->count() }})
                            </button>
                            <button type="button" onclick="switchTab('tab-presensi')" id="btn-tab-presensi" class="tab-btn px-3 py-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-300 transition">
                                Presensi Kehadiran
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: KEUANGAN & UKT -->
                    <div id="tab-spp" class="tab-content space-y-4">
                        <!-- Metrics Stats -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-zinc-950 p-4 rounded-lg border border-zinc-800">
                                <h3 class="text-zinc-500 text-[10px] font-mono font-semibold uppercase tracking-wider mb-1">Total Tunggakan UKT</h3>
                                <p class="text-xl font-bold font-mono text-zinc-100">
                                    Rp {{ number_format($info['total_rupiah'], 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-zinc-500 mt-1 font-mono">{{ $info['total_bulan'] }} periode belum lunas</p>
                            </div>

                            <div class="bg-zinc-950 p-4 rounded-lg border border-zinc-800">
                                <h3 class="text-zinc-500 text-[10px] font-mono font-semibold uppercase tracking-wider mb-1">Total Terbayar</h3>
                                <p class="text-xl font-bold font-mono text-zinc-100">
                                    Rp {{ number_format($siswa->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-zinc-500 mt-1 font-mono">{{ $siswa->pembayarans->count() }} transaksi lunas</p>
                            </div>

                            <div class="bg-zinc-950 p-4 rounded-lg border border-zinc-800">
                                <h3 class="text-zinc-500 text-[10px] font-mono font-semibold uppercase tracking-wider mb-1">Metode Pembayaran</h3>
                                <p class="text-xs font-semibold text-zinc-300 mt-1">Virtual Account BNI:</p>
                                <p class="text-sm font-mono font-bold text-zinc-100 select-all">988-1234-{{ $siswa->nisn }}</p>
                                <p class="text-[10px] text-zinc-500 mt-0.5">atau melalui Kasir BAAK Kampus</p>
                            </div>
                        </div>

                        <!-- Unpaid Months Warning -->
                        @if($info['total_bulan'] > 0)
                            <div class="bg-zinc-950 rounded-lg p-4 border border-zinc-800">
                                <h3 class="text-zinc-300 font-semibold text-xs uppercase tracking-wider mb-2 font-mono">
                                    Daftar Periode UKT Belum Terbayar:
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($info['list_bulan'] as $bulan)
                                        <span class="bg-zinc-900 text-zinc-300 border border-zinc-800 px-3 py-1 rounded text-xs font-mono">
                                            {{ $bulan }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Payment History Table -->
                        <div class="bg-zinc-950 rounded-lg border border-zinc-800 overflow-hidden">
                            <div class="p-3 border-b border-zinc-800 flex justify-between items-center font-mono">
                                <h3 class="font-bold text-xs uppercase tracking-wider text-zinc-300">Riwayat Pembayaran UKT</h3>
                                <span class="text-xs text-zinc-500">{{ $siswa->pembayarans->count() }} Transaksi</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs font-mono">
                                    <thead>
                                        <tr class="border-b border-zinc-800 bg-zinc-900 text-zinc-500 uppercase tracking-wider text-[10px]">
                                            <th class="py-2.5 px-3">No. Kwitansi</th>
                                            <th class="py-2.5 px-3">Periode UKT</th>
                                            <th class="py-2.5 px-3">Tanggal</th>
                                            <th class="py-2.5 px-3">Nominal</th>
                                            <th class="py-2.5 px-3">Petugas BAAK</th>
                                            <th class="py-2.5 px-3 text-right">Kwitansi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-800">
                                        @forelse($siswa->pembayarans->sortByDesc('tgl_bayar') as $p)
                                            <tr class="hover:bg-zinc-900 transition">
                                                <td class="py-2.5 px-3 font-bold text-zinc-100">
                                                    KWT-{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}
                                                </td>
                                                <td class="py-2.5 px-3 text-zinc-300">
                                                    {{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}
                                                </td>
                                                <td class="py-2.5 px-3 text-zinc-400">
                                                    {{ $p->tgl_bayar }}
                                                </td>
                                                <td class="py-2.5 px-3 font-bold text-zinc-100">
                                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                                </td>
                                                <td class="py-2.5 px-3 text-zinc-400">
                                                    {{ $p->petugas->name ?? 'Kasir BAAK' }}
                                                </td>
                                                <td class="py-2.5 px-3 text-right">
                                                    <a href="{{ route('web.pembayaran.cetak', $p->id) }}" target="_blank"
                                                        class="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 rounded text-xs transition inline-flex items-center gap-1">
                                                        <span>Cetak</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-zinc-500">Belum ada data riwayat pembayaran UKT.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: NILAI & KHS -->
                    <div id="tab-nilai" class="tab-content hidden space-y-4">
                        <div class="bg-zinc-950 rounded-lg border border-zinc-800 overflow-hidden">
                            <div class="p-3 border-b border-zinc-800 flex justify-between items-center font-mono">
                                <h3 class="font-bold text-xs uppercase tracking-wider text-zinc-300">Capaian Akademik &amp; KHS</h3>
                                <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank" class="text-xs text-zinc-300 hover:underline">
                                    Cetak Lembar E-KHS &rarr;
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs font-mono">
                                    <thead>
                                        <tr class="border-b border-zinc-800 bg-zinc-900 text-zinc-500 uppercase tracking-wider text-[10px]">
                                            <th class="py-2.5 px-3">Mata Kuliah</th>
                                            <th class="py-2.5 px-3 text-center">KKM</th>
                                            <th class="py-2.5 px-3 text-center">Tugas</th>
                                            <th class="py-2.5 px-3 text-center">UTS</th>
                                            <th class="py-2.5 px-3 text-center">UAS</th>
                                            <th class="py-2.5 px-3 text-center font-bold">Akhir</th>
                                            <th class="py-2.5 px-3 text-center">Predikat</th>
                                            <th class="py-2.5 px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-800">
                                        @forelse($siswa->nilais as $n)
                                            <tr class="hover:bg-zinc-900 transition">
                                                <td class="py-2.5 px-3 font-sans">
                                                    <div class="font-semibold text-zinc-100">{{ $n->mapel?->nama_mapel }}</div>
                                                    <div class="text-[10px] text-zinc-500 font-mono">Dosen: {{ $n->guru?->nama_guru ?? '-' }}</div>
                                                </td>
                                                <td class="py-2.5 px-3 text-center text-zinc-400">{{ $n->mapel?->kkm }}</td>
                                                <td class="py-2.5 px-3 text-center text-zinc-300">{{ (float) $n->nilai_tugas }}</td>
                                                <td class="py-2.5 px-3 text-center text-zinc-300">{{ (float) $n->nilai_uts }}</td>
                                                <td class="py-2.5 px-3 text-center text-zinc-300">{{ (float) $n->nilai_uas }}</td>
                                                <td class="py-2.5 px-3 text-center font-bold text-zinc-100">{{ (float) $n->nilai_akhir }}</td>
                                                <td class="py-2.5 px-3 text-center font-bold text-zinc-200">{{ $n->predikat }}</td>
                                                <td class="py-2.5 px-3">
                                                    @if($n->nilai_akhir >= ($n->mapel?->kkm ?? 75))
                                                        <span class="text-zinc-200 font-semibold text-[11px]">✓ Lulus</span>
                                                    @else
                                                        <span class="text-zinc-400 font-semibold text-[11px]">Remedi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-8 text-center text-zinc-500">Belum ada data nilai akademik yang diinput untuk mahasiswa ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: PRESENSI KEHADIRAN -->
                    <div id="tab-presensi" class="tab-content hidden space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 font-mono">
                            <div class="p-3.5 bg-zinc-950 border border-zinc-800 rounded-lg text-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Hadir</span>
                                <span class="text-xl font-bold text-zinc-100">{{ $kehadiran['hadir'] }} Sesi</span>
                            </div>
                            <div class="p-3.5 bg-zinc-950 border border-zinc-800 rounded-lg text-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Izin</span>
                                <span class="text-xl font-bold text-zinc-100">{{ $kehadiran['izin'] }} Sesi</span>
                            </div>
                            <div class="p-3.5 bg-zinc-950 border border-zinc-800 rounded-lg text-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Sakit</span>
                                <span class="text-xl font-bold text-zinc-100">{{ $kehadiran['sakit'] }} Sesi</span>
                            </div>
                            <div class="p-3.5 bg-zinc-950 border border-zinc-800 rounded-lg text-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Alpa</span>
                                <span class="text-xl font-bold text-zinc-100">{{ $kehadiran['alpa'] }} Sesi</span>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="text-center py-12 text-zinc-500 bg-zinc-950/50 rounded-lg border border-dashed border-zinc-800">
                    <p class="text-xs font-semibold text-zinc-400 font-mono">Silakan masukkan NIM atau NISN mahasiswa pada kolom pencarian</p>
                    <p class="text-[11px] text-zinc-500 mt-1">Hasil studi E-KHS, presensi perkuliahan, dan status pembayaran UKT akan ditampilkan.</p>
                </div>
            @endif

        </div>
        
        <!-- Footer Info -->
        <div class="bg-zinc-950 p-4 text-center text-zinc-500 text-xs border-t border-zinc-800 flex flex-col sm:flex-row justify-between items-center gap-2 font-mono">
            <span>&copy; {{ date('Y') }} SIAKAD Enterprise &bull; Layanan Akademik Universita</span>
            <div class="flex items-center gap-3">
                <a href="{{ url('/api/docs') }}" target="_blank" class="hover:text-zinc-300 transition">API Documentation</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-zinc-100 font-semibold transition">Portal Auth</a>
            </div>
        </div>
    </div>

    <!-- Floating Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 flex flex-col gap-2.5 max-w-md w-auto sm:w-96 pointer-events-none"></div>

    <!-- Global Modal Alert Dialog -->
    <div id="alertModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 transition-opacity duration-150 animate-modal-backdrop">
        <div class="bg-zinc-900 border border-zinc-800 text-zinc-100 rounded-xl max-w-md w-full p-5 shadow-2xl space-y-4 animate-modal-enter">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div id="modalIconContainer" class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono font-bold text-xs shrink-0">
                        !
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-sm text-zinc-100">Notifikasi Sistem</h3>
                        <span id="modalTypeBadge" class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider">ALERT</span>
                    </div>
                </div>
                <button type="button" onclick="closeAlertModal()" class="text-zinc-400 hover:text-zinc-200 text-sm font-mono transition">&times;</button>
            </div>
            <div id="modalBody" class="text-xs text-zinc-300 leading-relaxed font-sans border-y border-zinc-800 py-3 max-h-60 overflow-y-auto">
                Pesan notifikasi sistem.
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAlertModal()" class="px-4 py-2 bg-zinc-100 text-zinc-900 hover:bg-zinc-200 active:scale-[0.97] rounded-lg text-xs font-semibold font-mono transition-all duration-150">
                    Tutup &bull; OK
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="text-[11px] text-zinc-600 text-center pb-4 font-mono">
        SIAKAD Enterprise Core &bull; UU PDP &amp; PDDikti Neofeeder Compliant
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('animate-card-in');
            });
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-zinc-100', 'text-zinc-100');
                el.classList.add('border-transparent', 'text-zinc-500');
            });

            const target = document.getElementById(tabId);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('animate-card-in');
            }
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-zinc-500');
                activeBtn.classList.add('border-zinc-100', 'text-zinc-100');
            }
        }

        function showAlertModal(title, message, type = 'error') {
            const modal = document.getElementById('alertModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalIconContainer = document.getElementById('modalIconContainer');
            const modalTypeBadge = document.getElementById('modalTypeBadge');

            if (!modal) return;

            modalTitle.textContent = title || (type === 'error' ? 'Pencarian Gagal' : 'Informasi');
            modalBody.innerHTML = message;
            modalTypeBadge.textContent = type.toUpperCase();

            if (type === 'error') {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-rose-950/60 border border-rose-800 text-rose-400 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '✕';
            } else {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 text-zinc-300 flex items-center justify-center font-mono font-bold text-xs shrink-0';
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
            toast.className = `pointer-events-auto p-4 rounded-xl shadow-2xl border flex items-start gap-3 bg-zinc-900 text-zinc-100 border-zinc-800 transition-all duration-150 backdrop-blur-md animate-toast-in`;

            let iconSymbol = type === 'error' ? '✕' : (type === 'success' ? '✓' : '!');
            let badgeBg = type === 'error' ? 'bg-rose-950/60 text-rose-300 border-rose-800' : 'bg-zinc-800 text-zinc-300 border-zinc-700';

            toast.innerHTML = `
                <div class="w-7 h-7 rounded-lg ${badgeBg} border flex items-center justify-center font-mono text-xs font-bold shrink-0">
                    ${iconSymbol}
                </div>
                <div class="flex-1 min-w-0 font-sans">
                    <div class="font-bold text-xs text-zinc-100">${title}</div>
                    <div class="text-[11px] text-zinc-400 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-zinc-400 hover:text-zinc-200 text-xs p-1 font-mono transition">&times;</button>
            `;

            container.appendChild(toast);
            setTimeout(() => { dismissToast(toast); }, 5000);
        }

        function dismissToast(element) {
            if (!element) return;
            element.classList.remove('animate-toast-in');
            element.classList.add('animate-toast-out');
            setTimeout(() => { element.remove(); }, 150);
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                showToast('error', 'Pencarian Gagal', '{{ session('error') }}');
                showAlertModal('Data Tidak Ditemukan', '{{ session('error') }}', 'error');
            @endif

            @if(session('success'))
                showToast('success', 'Berhasil', '{{ session('success') }}');
            @endif
        });
    </script>
</body>
</html>