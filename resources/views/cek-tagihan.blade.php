<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Layanan Mahasiswa - SIAKAD Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-3 sm:p-6 bg-slate-900 text-slate-800">

    <!-- Top Navigation Bar -->
    <div class="w-full max-w-4xl flex justify-between items-center py-2 px-4 mb-4 text-white">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-black text-white text-base shadow-md"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg></div>
            <div>
                <span class="font-extrabold text-base tracking-tight block">SIAKAD Enterprise</span>
                <span class="text-[11px] text-indigo-300 block">Portal Layanan Mandiri Mahasiswa &amp; Civitas Akademika (SIAKAD &amp; UKT)</span>
            </div>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <a href="{{ url('/api/docs') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-medium transition">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> API Docs
            </a>
            <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3.5 py-1.5 rounded-lg font-semibold transition shadow-sm">
                Login Portal &rarr;
            </a>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100 mb-8">
        
        <!-- Header Hero Banner -->
        <div class="bg-gradient-to-r from-indigo-700 via-blue-800 to-slate-900 p-6 md:p-8 text-white relative overflow-hidden">
            <div class="relative z-10 text-center max-w-xl mx-auto">
                <span class="px-3 py-1 bg-white/15 text-indigo-100 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2 inline-block">
                    Sistem Informasi Akademik &amp; Keuangan Mandiri
                </span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">Cek Nilai, E-KHS, Presensi &amp; UKT</h1>
                <p class="text-indigo-100 text-xs md:text-sm mt-1">Masukkan NIM / NISN mahasiswa untuk melihat hasil belajar akademik dan status pembayaran UKT.</p>
            </div>
        </div>

        <div class="p-5 md:p-8">
            <!-- Search Form -->
            <form action="{{ route('cek.search') }}" method="POST" class="mb-6">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="nisn" placeholder="Masukkan NIM / NISN mahasiswa..." required
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition shadow-sm text-sm md:text-base font-mono"
                            value="{{ request('nisn', $siswa->nisn ?? '') }}">
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl transition shadow-md hover:shadow-lg active:scale-95 flex justify-center items-center gap-2 text-sm whitespace-nowrap">
                        <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span> Periksa Data
                    </button>
                </div>

                @if(session('error'))
                    <div class="mt-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-xl text-xs sm:text-sm shadow-sm flex items-center justify-between" role="alert">
                        <div class="flex items-center gap-2">
                            <span class="text-base"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>️</span>
                            <span>{{ session('error') }}</span>
                        </div>
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

                <div class="space-y-6 animate-fade-in">
                    
                    <!-- Student Identity Card -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">Siswa Aktif</span>
                                <span class="text-xs text-slate-400">NIS: {{ $siswa->nis }}</span>
                            </div>
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 mt-1">{{ $siswa->nama }}</h2>
                            <div class="text-slate-600 text-xs mt-1 flex flex-wrap items-center gap-2">
                                <span><strong class="text-slate-700">NISN:</strong> <span class="font-mono">{{ $siswa->nisn }}</span></span>
                                <span class="text-slate-300">&bull;</span>
                                <span><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }} ({{ $siswa->kelas->kompetensi_keahlian }})</span>
                                <span class="text-slate-300">&bull;</span>
                                <span><strong>Tarif SPP:</strong> Rp {{ number_format($siswa->spp->nominal, 0, ',', '.') }}/bln</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span> Cetak E-Rapor
                            </a>
                            <a href="{{ route('web.siswa.suratTagihan', $siswa->id) }}" target="_blank"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span> Surat Tagihan
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Portal Tabs -->
                    <div class="border-b border-slate-200">
                        <div class="flex space-x-2 text-xs font-bold" id="portalTabs">
                            <button type="button" onclick="switchTab('tab-spp')" id="btn-tab-spp" class="tab-btn px-4 py-2.5 border-b-2 border-blue-600 text-blue-600 transition flex items-center gap-1.5">
                                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></span> Keuangan &amp; SPP
                            </button>
                            <button type="button" onclick="switchTab('tab-nilai')" id="btn-tab-nilai" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5">
                                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span> Nilai &amp; Rapor Akademik ({{ $siswa->nilais->count() }})
                            </button>
                            <button type="button" onclick="switchTab('tab-presensi')" id="btn-tab-presensi" class="tab-btn px-4 py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5">
                                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></span> Presensi Kehadiran
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: KEUANGAN & SPP -->
                    <div id="tab-spp" class="tab-content space-y-6">
                        <!-- Metrics Stats -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-rose-50 to-white p-5 rounded-2xl border border-rose-200 shadow-sm">
                                <h3 class="text-rose-600 text-[11px] font-bold uppercase tracking-wider mb-1">Total Tunggakan SPP</h3>
                                <p class="text-2xl font-black text-rose-900">
                                    Rp {{ number_format($info['total_rupiah'], 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-rose-600/80 mt-1 font-medium">{{ $info['total_bulan'] }} bulan belum diselesaikan</p>
                            </div>

                            <div class="bg-gradient-to-br from-emerald-50 to-white p-5 rounded-2xl border border-emerald-200 shadow-sm">
                                <h3 class="text-emerald-600 text-[11px] font-bold uppercase tracking-wider mb-1">Total Terbayar</h3>
                                <p class="text-2xl font-black text-emerald-900">
                                    Rp {{ number_format($siswa->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-emerald-600/80 mt-1 font-medium">{{ $siswa->pembayarans->count() }} transaksi lunas</p>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-2xl border border-blue-200 shadow-sm">
                                <h3 class="text-blue-600 text-[11px] font-bold uppercase tracking-wider mb-1">Metode Pembayaran</h3>
                                <p class="text-xs font-bold text-blue-950 mt-1">BNI Virtual Account:</p>
                                <p class="text-sm font-mono font-black text-blue-700 select-all">988-1234-{{ $siswa->nisn }}</p>
                                <p class="text-[10px] text-blue-500 mt-0.5">atau melalui loket kasir BAAK / Keuangan Kampus</p>
                            </div>
                        </div>

                        <!-- Unpaid Months Warning -->
                        @if($info['total_bulan'] > 0)
                            <div class="bg-rose-50 rounded-2xl p-5 border border-rose-200">
                                <h3 class="text-rose-900 font-bold text-xs uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>️</span> Daftar Bulan yang Perlu Dibayar:
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($info['list_bulan'] as $bulan)
                                        <span class="bg-white text-rose-700 border border-rose-300 px-3.5 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                                            {{ $bulan }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Payment History Table -->
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Riwayat Pembayaran &amp; Unduh Kwitansi</h3>
                                <span class="text-xs text-slate-500 font-medium">{{ $siswa->pembayarans->count() }} Pembayaran Tercatat</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-500 uppercase tracking-wider">
                                            <th class="py-3 px-4 font-semibold">No. Kwitansi</th>
                                            <th class="py-3 px-4 font-semibold">Periode SPP</th>
                                            <th class="py-3 px-4 font-semibold">Tanggal Bayar</th>
                                            <th class="py-3 px-4 font-semibold">Nominal</th>
                                            <th class="py-3 px-4 font-semibold">Petugas Penerima</th>
                                            <th class="py-3 px-4 font-semibold text-right">Kwitansi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($siswa->pembayarans->sortByDesc('tgl_bayar') as $p)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-3 px-4 font-mono font-bold text-blue-700">
                                                    KWT-{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}
                                                </td>
                                                <td class="py-3 px-4 font-bold text-slate-900">
                                                    {{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}
                                                </td>
                                                <td class="py-3 px-4 text-slate-600 font-mono">
                                                    {{ $p->tgl_bayar }}
                                                </td>
                                                <td class="py-3 px-4 font-bold text-emerald-700">
                                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">
                                                    {{ $p->petugas->name ?? 'Petugas Loket' }}
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    <a href="{{ route('web.pembayaran.cetak', $p->id) }}" target="_blank"
                                                        class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                                                        <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></span> Cetak
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data riwayat pembayaran SPP.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: NILAI & RAPOR AKADEMIK -->
                    <div id="tab-nilai" class="tab-content hidden space-y-4">
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Daftar Nilai Capaian Kompetensi Siswa</h3>
                                <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold">
                                    Cetak Lembar Rapor Lengkap &rarr;
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-500 uppercase tracking-wider">
                                            <th class="py-3 px-4 font-semibold">Mata Pelajaran</th>
                                            <th class="py-3 px-4 font-semibold text-center">KKM</th>
                                            <th class="py-3 px-4 font-semibold text-center">Tugas</th>
                                            <th class="py-3 px-4 font-semibold text-center">UTS</th>
                                            <th class="py-3 px-4 font-semibold text-center">UAS</th>
                                            <th class="py-3 px-4 font-semibold text-center font-bold">Nilai Akhir</th>
                                            <th class="py-3 px-4 font-semibold text-center">Predikat</th>
                                            <th class="py-3 px-4 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($siswa->nilais as $n)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="font-bold text-slate-900">{{ $n->mapel?->nama_mapel }}</div>
                                                    <div class="text-[10px] text-slate-400">Guru: {{ $n->guru?->nama_guru ?? '-' }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-center font-mono">{{ $n->mapel?->kkm }}</td>
                                                <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_tugas }}</td>
                                                <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uts }}</td>
                                                <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uas }}</td>
                                                <td class="py-3 px-4 text-center font-mono font-bold text-slate-900">{{ (float) $n->nilai_akhir }}</td>
                                                <td class="py-3 px-4 text-center font-bold">{{ $n->predikat }}</td>
                                                <td class="py-3 px-4">
                                                    @if($n->nilai_akhir >= ($n->mapel?->kkm ?? 75))
                                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Tuntas</span>
                                                    @else
                                                        <span class="text-rose-700 font-bold text-[11px]"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Remedi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-8 text-center text-slate-400">Belum ada data nilai akademik yang diinput untuk siswa ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: PRESENSI KEHADIRAN -->
                    <div id="tab-presensi" class="tab-content hidden space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-center">
                                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Hadir</span>
                                <span class="text-2xl font-black text-emerald-900">{{ $kehadiran['hadir'] }} Hari</span>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl text-center">
                                <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Izin</span>
                                <span class="text-2xl font-black text-blue-900">{{ $kehadiran['izin'] }} Hari</span>
                            </div>
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-center">
                                <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Sakit</span>
                                <span class="text-2xl font-black text-amber-900">{{ $kehadiran['sakit'] }} Hari</span>
                            </div>
                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-center">
                                <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">Alpa</span>
                                <span class="text-2xl font-black text-rose-900">{{ $kehadiran['alpa'] }} Hari</span>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="text-center py-12 text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <div class="text-4xl mb-2"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
                    <p class="text-sm font-semibold text-slate-600">Silakan masukkan 10 digit NISN pada kolom di atas</p>
                    <p class="text-xs text-slate-400 mt-1">Data hasil belajar akademik (Nilai, E-Rapor), presensi kehadiran, serta riwayat tagihan SPP akan ditampilkan.</p>
                </div>
            @endif

        </div>
        
        <!-- Footer Info -->
        <div class="bg-slate-50 p-4 text-center text-slate-400 text-xs border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>&copy; {{ date('Y') }} Universitas SIAKAD Enterprise &bull; Layanan Akademik &amp; Keuangan Kampus</span>
            <div class="flex items-center gap-3">
                <a href="{{ url('/api/docs') }}" target="_blank" class="hover:text-slate-600 transition">Dokumentasi API</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-blue-600 font-semibold transition">Area Civitas / BAAK</a>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="text-xs text-slate-400 text-center pb-4">
        SIAKAD &amp; UKT Enterprise Pro &bull; Laravel Monolith &amp; Sanctum REST API
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-blue-600', 'text-blue-600');
                el.classList.add('border-transparent', 'text-slate-500');
            });

            document.getElementById(tabId).classList.remove('hidden');
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-slate-500');
                activeBtn.classList.add('border-blue-600', 'text-blue-600');
            }
        }
    </script>
</body>
</html>