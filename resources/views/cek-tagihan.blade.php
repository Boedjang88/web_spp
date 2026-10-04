<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Siswa - Cek Tagihan & Kwitansi SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-3 sm:p-6 bg-slate-900 text-slate-800">

    <!-- Top Navigation Bar -->
    <div class="w-full max-w-4xl flex justify-between items-center py-2 px-4 mb-4 text-white">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🎓</span>
            <div>
                <span class="font-extrabold text-base tracking-tight block">SMK Merdeka Belajar</span>
                <span class="text-[11px] text-blue-200 block">Portal Layanan Mandiri Siswa & Wali Murid</span>
            </div>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <a href="{{ url('/api/docs') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-medium transition">
                ⚡ API Docs
            </a>
            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-1.5 rounded-lg font-semibold transition shadow-sm">
                Login Petugas &rarr;
            </a>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100 mb-8">
        
        <!-- Header Hero Banner -->
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 p-6 md:p-8 text-white relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-blue-400/20 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 text-center max-w-xl mx-auto">
                <span class="px-3 py-1 bg-white/15 text-blue-100 rounded-full text-xs font-bold uppercase tracking-wider mb-2 inline-block">
                    Cek SPP Real-Time
                </span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">Cek Tagihan & Riwayat Pembayaran</h1>
                <p class="text-blue-100 text-xs md:text-sm mt-1">Masukkan 10 digit NISN siswa untuk memeriksa status iuran SPP dan mengunduh kwitansi resmi.</p>
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
                        <input type="text" name="nisn" placeholder="Masukkan 10 digit NISN (cth: 0051234567)..." required
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition shadow-sm text-sm md:text-base font-mono"
                            value="{{ request('nisn', $siswa->nisn ?? '') }}">
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl transition shadow-md hover:shadow-lg active:scale-95 flex justify-center items-center gap-2 text-sm whitespace-nowrap">
                        <span>🔍</span> Periksa Data
                    </button>
                </div>

                @if(session('error'))
                    <div class="mt-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-xl text-xs sm:text-sm shadow-sm flex items-center justify-between" role="alert">
                        <div class="flex items-center gap-2">
                            <span class="text-base">⚠️</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </form>

            @if(isset($siswa))
                @php $info = $siswa->info_tunggakan; @endphp

                <div class="space-y-6 animate-fade-in">
                    
                    <!-- Student Identity Card -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">Siswa Terdaftar</span>
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
                            <a href="{{ route('web.siswa.suratTagihan', $siswa->id) }}" target="_blank"
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                                <span>📄</span> Cetak Surat Tagihan
                            </a>

                            @if($info['total_bulan'] == 0)
                                <div class="flex items-center gap-1.5 bg-emerald-100 text-emerald-800 px-4 py-2 rounded-xl font-bold text-xs shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    LUNAS
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 bg-rose-100 text-rose-800 px-4 py-2 rounded-xl font-bold text-xs shadow-sm">
                                    <span>⚠️</span> Menunggak {{ $info['total_bulan'] }} Bulan
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-rose-50 to-white p-5 rounded-2xl border border-rose-200 shadow-sm">
                            <h3 class="text-rose-600 text-[11px] font-bold uppercase tracking-wider mb-1">Total Tunggakan</h3>
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
                            <p class="text-[10px] text-blue-500 mt-0.5">atau melalui loket kasir sekolah</p>
                        </div>
                    </div>

                    <!-- Unpaid Months Warning -->
                    @if($info['total_bulan'] > 0)
                        <div class="bg-rose-50 rounded-2xl p-5 border border-rose-200">
                            <h3 class="text-rose-900 font-bold text-xs uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <span>⚠️</span> Daftar Bulan yang Perlu Dibayar:
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

                    <!-- Payment History & Receipt Download Table -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Riwayat Pembayaran & Unduh Kwitansi</h3>
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
                                                    <span>🖨️</span> Cetak Kwitansi
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-slate-400">
                                                Belum ada data riwayat pembayaran SPP untuk siswa ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @else
                <div class="text-center py-12 text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <div class="text-4xl mb-2">🔍</div>
                    <p class="text-sm font-semibold text-slate-600">Silakan masukkan 10 digit NISN pada kolom di atas</p>
                    <p class="text-xs text-slate-400 mt-1">Data tagihan SPP dan riwayat kwitansi pembayaran akan langsung ditampilkan secara transparan.</p>
                </div>
            @endif

        </div>
        
        <!-- Footer Info -->
        <div class="bg-slate-50 p-4 text-center text-slate-400 text-xs border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>&copy; {{ date('Y') }} SMK Merdeka Belajar &bull; Layanan Keuangan Sekolah</span>
            <div class="flex items-center gap-3">
                <a href="{{ url('/api/docs') }}" target="_blank" class="hover:text-slate-600 transition">Dokumentasi API</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-blue-600 font-semibold transition">Area Pegawai</a>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="text-xs text-slate-400 text-center pb-4">
        Aplikasi SPP Sekolah Pro &bull; Laravel Monolith &amp; Sanctum REST API
    </div>

</body>
</html>