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
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-3 sm:p-6 bg-zinc-950 text-zinc-100 antialiased">

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
                    $kehadiran = [
                        'hadir' => $siswa->presensis->where('status', 'Hadir')->count(),
                        'izin' => $siswa->presensis->where('status', 'Izin')->count(),
                        'sakit' => $siswa->presensis->where('status', 'Sakit')->count(),
                        'alpa' => $siswa->presensis->where('status', 'Alpa')->count(),
                    ];
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

    <!-- Bottom Footer -->
    <div class="text-[11px] text-zinc-600 text-center pb-4 font-mono">
        SIAKAD Enterprise Core &bull; UU PDP &amp; PDDikti Neofeeder Compliant
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-zinc-100', 'text-zinc-100');
                el.classList.add('border-transparent', 'text-zinc-500');
            });

            document.getElementById(tabId).classList.remove('hidden');
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-zinc-500');
                activeBtn.classList.add('border-zinc-100', 'text-zinc-100');
            }
        }
    </script>
</body>
</html>