@extends('layouts.app')

@section('title', 'Dashboard SIAKAD & Keuangan')

@section('content')
<div class="space-y-6">

    <!-- Header & Welcome Banner (Calm & Modern) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-indigo-950 rounded-2xl p-6 md:p-8 text-white shadow-soft relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-slate-800">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-slate-200 border border-white/10">
                    Sistem Akademik &amp; Keuangan Terpadu
                </span>
                <span class="text-xs text-indigo-300">T.A. 2025/2026</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Pantau proses pembelajaran akademik, data pengajar, jadwal kelas, e-rapor, serta arus kas pembayaran SPP secara real-time.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('web.pembayaran.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-sm transition inline-flex items-center gap-1.5">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Catat SPP
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/15 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/10">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span> Input Nilai
            </a>
        </div>
    </div>

    <!-- Core Metrics Stats Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Peserta Didik Aktif</span>
                <div class="text-2xl font-bold text-slate-900">{{ $totalSiswa }} Siswa</div>
                <p class="text-[11px] text-slate-500 mt-1">Tersebar di {{ $totalKelas }} Kelas</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0 border border-blue-100">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Dewan Guru</span>
                <div class="text-2xl font-bold text-slate-900">{{ $totalGuru }} Guru</div>
                <p class="text-[11px] text-slate-500 mt-1">{{ $totalMapel }} Mata Pelajaran</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0 border border-indigo-100">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <!-- Total Pemasukan SPP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Total Kas SPP</span>
                <div class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Bulan Ini: Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-100">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Presensi Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Kehadiran Hari Ini</span>
                <div class="text-2xl font-bold text-slate-900">{{ $presensiHariIni['hadir'] }} Hadir</div>
                <p class="text-[11px] text-slate-500 mt-1">{{ $presensiHariIni['izin'] + $presensiHariIni['sakit'] }} Izin/Sakit &bull; {{ $presensiHariIni['alpa'] }} Alpa</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0 border border-amber-100">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </div>
    </div>

    <!-- Academic & Financial Widgets (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Financial Revenue Trends Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Tren Penerimaan Kas SPP (Tahun {{ $currentYear }})</h2>
                    <p class="text-xs text-slate-400">Statistik transaksi pembayaran SPP bulanan</p>
                </div>
                <span class="text-[10px] font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 uppercase tracking-wider">
                    Kas Bulanan
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Today's Class Schedule Widget -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-bold text-slate-900 text-sm">Jadwal Hari {{ $hariIni }}</h2>
                        <p class="text-[11px] text-slate-400">Sesi pelajaran aktif</p>
                    </div>
                    <a href="{{ route('web.jadwal.index') }}" class="text-xs text-indigo-600 hover:underline font-semibold">Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($jadwalHariIni as $j)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-semibold text-slate-800 block">{{ $j->mapel?->nama_mapel }}</span>
                                <span class="text-[11px] text-slate-400">{{ $j->kelas?->nama_kelas }} &bull; {{ $j->guru?->nama_guru }}</span>
                            </div>
                            <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 font-mono text-[10px] font-bold text-indigo-700 shadow-2xs">
                                {{ substr($j->jam_mulai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada jadwal pelajaran untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('web.presensi.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    Buka Formulir Presensi Kehadiran Siswa &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Transactions & Class Distribution (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Transaksi Pembayaran SPP Terbaru</h2>
                    <p class="text-xs text-slate-400">6 transaksi pembayaran terakhir yang berhasil diverifikasi</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                            <th class="py-3 px-4 font-semibold">No. Kwitansi</th>
                            <th class="py-3 px-4 font-semibold">Nama Siswa</th>
                            <th class="py-3 px-4 font-semibold">Periode SPP</th>
                            <th class="py-3 px-4 font-semibold">Nominal</th>
                            <th class="py-3 px-4 font-semibold text-right">Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaru as $t)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-indigo-600">
                                    KWT-{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-900">{{ $t->siswa?->nama }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $t->siswa?->kelas?->nama_kelas }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $t->bulan_dibayar }} {{ $t->tahun_dibayar }}
                                </td>
                                <td class="py-3 px-4 font-bold text-emerald-700">
                                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('web.pembayaran.cetak', $t->id) }}" target="_blank"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></span> Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada transaksi pembayaran SPP yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1 Col: Student Distribution Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-sm mb-1">Distribusi Siswa per Kelas</h2>
                <p class="text-xs text-slate-400 mb-4">Proporsi siswa aktif di setiap kelas</p>
                <div class="h-48 flex items-center justify-center">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 text-center">
                <span class="text-xs text-slate-500 font-medium">Total: <strong>{{ $totalSiswa }} Siswa</strong> di <strong>{{ $totalKelas }} Kelas</strong></span>
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
                label: 'Pemasukan SPP (Rp)',
                data: @json($chartData),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 3.5,
                pointHoverRadius: 5,
                pointBackgroundColor: '#4f46e5'
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
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                            return 'Rp ' + value;
                        },
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
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
                    '#6366f1', '#3b82f6', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 10 } }
                }
            }
        }
    });
</script>
@endpush
@endsection
