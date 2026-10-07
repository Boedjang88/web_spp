@extends('layouts.app')

@section('title', 'Dashboard SIAKAD Enterprise & Keuangan')

@section('content')
<div class="space-y-6">

    <!-- Executive Header Banner -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 rounded-2xl p-6 text-white border border-blue-900/50 shadow-soft flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-blue-200 border border-white/10 font-mono">
                    Sistem Akademik &amp; Keuangan Perguruan Tinggi
                </span>
                <span class="text-xs text-blue-200/80 font-mono">T.A. 2025/2026</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Executive Dashboard &bull; {{ auth()->user()->name }}</h1>
            <p class="text-slate-300 text-xs mt-1 max-w-xl leading-relaxed">
                Ringkasan real-time aktivitas akademik, presensi mahasiswa, jadwal perkuliahan, dan arus kas pembayaran UKT.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('web.pembayaran.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs transition inline-flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat UKT</span>
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2.5 rounded-xl text-xs transition inline-flex items-center gap-1.5 border border-white/10 backdrop-blur">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input Nilai</span>
            </a>
        </div>
    </div>

    <!-- Core Metrics Stats Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Mahasiswa -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft hover:shadow-card transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Mahasiswa Aktif</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">{{ $totalSiswa }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Tersebar di {{ $totalKelas }} Kelas Kuliah</span>
            </div>
        </div>

        <!-- Total Dosen -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft hover:shadow-card transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Dosen Pengajar</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">{{ $totalGuru }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <span>Mengampu {{ $totalMapel }} Mata Kuliah</span>
            </div>
        </div>

        <!-- Total Pemasukan UKT -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft hover:shadow-card transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Total Kas UKT</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Bulan Ini: Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Presensi Hari Ini -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft hover:shadow-card transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Kehadiran Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">{{ $presensiHariIni['hadir'] }} Sesi</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>{{ $presensiHariIni['izin'] + $presensiHariIni['sakit'] }} Izin/Sakit &bull; {{ $presensiHariIni['alpa'] }} Alpa</span>
            </div>
        </div>
    </div>

    <!-- Academic & Financial Widgets (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Financial Revenue Trends Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Tren Penerimaan Kas UKT ({{ $currentYear }})</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Statistik penerimaan kas UKT bulanan</p>
                </div>
                <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    REALTIME LEDGER
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Today's Class Schedule Widget -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Jadwal Kuliah {{ $hariIni }}</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Sesi perkuliahan aktif</p>
                    </div>
                    <a href="{{ route('web.jadwal.index') }}" class="text-[11px] text-blue-600 dark:text-blue-400 font-bold hover:underline">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($jadwalHariIni as $j)
                        <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs">{{ $j->mapel?->nama_mapel }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">{{ $j->kelas?->nama_kelas }} &bull; {{ $j->guru?->nama_guru }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-mono text-[10px] font-bold text-blue-600 dark:text-blue-400 shadow-2xs">
                                {{ substr($j->jam_mulai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs font-medium">
                            Tidak ada jadwal perkuliahan untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('web.presensi.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                    <span>Kelola Presensi Kehadiran</span> &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Transactions & Class Distribution (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Recent Transactions -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Pembayaran UKT Terakhir</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">6 transaksi pembayaran UKT terverifikasi</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Semua Transaksi &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-5">No. Kwitansi</th>
                            <th class="py-3 px-5">Mahasiswa</th>
                            <th class="py-3 px-5">Periode</th>
                            <th class="py-3 px-5">Nominal</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
                        @forelse($transaksiTerbaru as $t)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3.5 px-5 font-bold text-slate-900 dark:text-slate-100">
                                    KWT-{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3.5 px-5 font-sans">
                                    <div class="font-bold text-slate-900 dark:text-slate-100">{{ $t->siswa?->nama }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $t->siswa?->kelas?->nama_kelas }}</div>
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 dark:text-slate-400 font-sans">
                                    {{ $t->bulan_dibayar }} {{ $t->tahun_dibayar }}
                                </td>
                                <td class="py-3.5 px-5 font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right font-sans">
                                    <a href="{{ route('web.pembayaran.cetak', $t->id) }}" target="_blank"
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950 hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-xl border border-blue-200 dark:border-blue-800 text-xs font-semibold transition inline-flex items-center gap-1 shadow-2xs">
                                        <span>Cetak</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-sans">Belum ada transaksi pembayaran UKT yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1 Col: Student Distribution Chart -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider mb-1">Distribusi Mahasiswa per Kelas</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">Proporsi mahasiswa di setiap kelas kuliah</p>
                <div class="h-48 flex items-center justify-center">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-mono font-medium">Total: {{ $totalSiswa }} Mahasiswa &bull; {{ $totalKelas }} Kelas</span>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const isInitialDark = document.documentElement.classList.contains('dark');
    const initGridColor = isInitialDark ? '#334155' : '#e2e8f0';
    const initTextColor = isInitialDark ? '#cbd5e1' : '#334155';

    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan UKT (Rp)',
                data: @json($chartData),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#1d4ed8'
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
                    grid: { color: initGridColor },
                    ticks: {
                        color: initTextColor,
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
                    ticks: { color: initTextColor, font: { size: 10, family: 'JetBrains Mono' } }
                }
            }
        }
    });

    // Class Distribution Doughnut Chart
    const ctxClass = document.getElementById('classChart').getContext('2d');
    const classChart = new Chart(ctxClass, {
        type: 'doughnut',
        data: {
            labels: @json($kelasLabels),
            datasets: [{
                data: @json($kelasData),
                backgroundColor: [
                    '#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#f43f5e', '#06b6d4', '#6366f1', '#14b8a6', '#ec4899', '#84cc16', '#0284c7', '#d97706'
                ],
                borderWidth: 2,
                borderColor: isInitialDark ? '#1c2541' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: initTextColor, boxWidth: 10, font: { size: 10, family: 'Plus Jakarta Sans' } }
                }
            }
        }
    });

    // Theme synchronization listener for Chart.js
    window.addEventListener('themeChanged', function(e) {
        const isDark = e.detail ? e.detail.isDark : document.documentElement.classList.contains('dark');
        const gridColor = isDark ? '#334155' : '#e2e8f0';
        const textColor = isDark ? '#cbd5e1' : '#334155';
        const cardBgColor = isDark ? '#1c2541' : '#ffffff';

        if (revenueChart && revenueChart.options.scales.y) {
            revenueChart.options.scales.y.grid.color = gridColor;
            revenueChart.options.scales.y.ticks.color = textColor;
            revenueChart.options.scales.x.ticks.color = textColor;
            revenueChart.update();
        }

        if (classChart) {
            classChart.data.datasets[0].borderColor = cardBgColor;
            if (classChart.options.plugins.legend) {
                classChart.options.plugins.legend.labels.color = textColor;
            }
            classChart.update();
        }
    });
</script>
@endpush
@endsection
