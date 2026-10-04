@extends('layouts.app')

@section('title', 'Dashboard SIAKAD Enterprise & Keuangan')

@section('content')
<div class="space-y-6">

    <!-- Executive Header Banner -->
    <div class="bg-zinc-900 dark:bg-zinc-950 rounded-xl p-5 md:p-6 text-white border border-zinc-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-zinc-800 text-zinc-300 border border-zinc-700 font-mono">
                    Sistem Akademik &amp; Keuangan Perguruan Tinggi
                </span>
                <span class="text-xs text-zinc-400 font-mono">T.A. 2025/2026</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Executive Dashboard &bull; {{ auth()->user()->name }}</h1>
            <p class="text-zinc-400 text-xs mt-1 max-w-xl leading-relaxed">
                Ringkasan real-time aktivitas akademik, presensi mahasiswa, jadwal perkuliahan, dan arus kas pembayaran UKT.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('web.pembayaran.create') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold px-3.5 py-2 rounded-lg text-xs transition inline-flex items-center gap-1.5 border border-zinc-300">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat UKT</span>
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-semibold px-3.5 py-2 rounded-lg text-xs transition inline-flex items-center gap-1.5 border border-zinc-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input Nilai</span>
            </a>
        </div>
    </div>

    <!-- Core Metrics Stats Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Mahasiswa -->
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500 block mb-1">Mahasiswa Aktif</span>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">{{ $totalSiswa }}</div>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono">Tersebar di {{ $totalKelas }} Kelas Kuliah</p>
        </div>

        <!-- Total Dosen -->
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500 block mb-1">Dosen Pengajar</span>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">{{ $totalGuru }}</div>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono">{{ $totalMapel }} Mata Kuliah</p>
        </div>

        <!-- Total Pemasukan UKT -->
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500 block mb-1">Total Kas UKT</span>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono">Bulan Ini: Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
        </div>

        <!-- Presensi Hari Ini -->
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500 block mb-1">Kehadiran Hari Ini</span>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">{{ $presensiHariIni['hadir'] }} Sesi</div>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono">{{ $presensiHariIni['izin'] + $presensiHariIni['sakit'] }} Izin/Sakit &bull; {{ $presensiHariIni['alpa'] }} Alpa</p>
        </div>
    </div>

    <!-- Academic & Financial Widgets (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left 2 Cols: Financial Revenue Trends Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-4 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div>
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs uppercase tracking-wider">Tren Penerimaan Kas UKT ({{ $currentYear }})</h2>
                    <p class="text-[11px] text-zinc-500">Statistik penerimaan kas UKT bulanan</p>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                    REALTIME LEDGER
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Today's Class Schedule Widget -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div>
                        <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs uppercase tracking-wider">Jadwal Kuliah {{ $hariIni }}</h2>
                        <p class="text-[11px] text-zinc-500">Sesi perkuliahan aktif</p>
                    </div>
                    <a href="{{ route('web.jadwal.index') }}" class="text-[11px] text-zinc-700 dark:text-zinc-300 hover:underline font-mono">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-2">
                    @forelse($jadwalHariIni as $j)
                        <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-200 block text-xs">{{ $j->mapel?->nama_mapel }}</span>
                                <span class="text-[11px] text-zinc-500">{{ $j->kelas?->nama_kelas }} &bull; {{ $j->guru?->nama_guru }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-semibold text-zinc-700 dark:text-zinc-300">
                                {{ substr($j->jam_mulai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            Tidak ada jadwal perkuliahan untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 text-center">
                <a href="{{ route('web.presensi.index') }}" class="text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition">
                    Kelola Presensi Kehadiran &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Transactions & Class Distribution (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left 2 Cols: Recent Transactions -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs uppercase tracking-wider">Pembayaran UKT Terakhir</h2>
                    <p class="text-[11px] text-zinc-500">6 transaksi pembayaran UKT terverifikasi</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-mono text-zinc-700 dark:text-zinc-300 hover:underline">
                    Semua Transaksi &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 uppercase tracking-wider text-[10px] font-semibold">
                            <th class="py-2.5 px-4">No. Kwitansi</th>
                            <th class="py-2.5 px-4">Mahasiswa</th>
                            <th class="py-2.5 px-4">Periode</th>
                            <th class="py-2.5 px-4">Nominal</th>
                            <th class="py-2.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                        @forelse($transaksiTerbaru as $t)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                <td class="py-2.5 px-4 font-bold text-zinc-900 dark:text-zinc-100">
                                    KWT-{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-2.5 px-4 font-sans">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $t->siswa?->nama }}</div>
                                    <div class="text-[10px] text-zinc-500 font-mono">{{ $t->siswa?->kelas?->nama_kelas }}</div>
                                </td>
                                <td class="py-2.5 px-4 text-zinc-600 dark:text-zinc-400 font-sans">
                                    {{ $t->bulan_dibayar }} {{ $t->tahun_dibayar }}
                                </td>
                                <td class="py-2.5 px-4 font-bold text-zinc-900 dark:text-zinc-100">
                                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-sans">
                                    <a href="{{ route('web.pembayaran.cetak', $t->id) }}" target="_blank"
                                        class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded border border-zinc-300 dark:border-zinc-700 text-xs font-medium transition inline-flex items-center gap-1">
                                        <span>Cetak</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-400 font-sans">Belum ada transaksi pembayaran UKT yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1 Col: Student Distribution Chart -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs uppercase tracking-wider mb-1">Distribusi Mahasiswa per Kelas</h2>
                <p class="text-[11px] text-zinc-500 mb-4">Proporsi mahasiswa di setiap kelas kuliah</p>
                <div class="h-48 flex items-center justify-center">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 text-center">
                <span class="text-xs text-zinc-500 font-mono">Total: {{ $totalSiswa }} Mahasiswa &bull; {{ $totalKelas }} Kelas</span>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan UKT (Rp)',
                data: @json($chartData),
                borderColor: '#18181b',
                backgroundColor: 'rgba(24, 24, 27, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.2,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#18181b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f4f4f5' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                            return 'Rp ' + value;
                        },
                        font: { size: 10, family: 'JetBrains Mono' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, family: 'JetBrains Mono' } }
                }
            }
        }
    });

    // Class Distribution Doughnut Chart
    const ctxClass = document.getElementById('classChart').getContext('2d');
    new Chart(ctxClass, {
        type: 'doughnut',
        data: {
            labels: @json($kelasLabels),
            datasets: [{
                data: @json($kelasData),
                backgroundColor: [
                    '#18181b', '#3f3f46', '#71717a', '#a1a1aa', '#d4d4d8', '#e4e4e7'
                ],
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 10, family: 'Plus Jakarta Sans' } }
                }
            }
        }
    });
</script>
@endpush
@endsection
