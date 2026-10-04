@extends('layouts.app')

@section('title', 'Dashboard SIAKAD & Keuangan')

@section('content')
<div class="space-y-6">

    <!-- Header & Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur">
                    Sistem Akademik &amp; Keuangan Sekolah Terpadu
                </span>
                <span class="text-xs text-blue-200">Tahun Ajaran 2025/2026</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-blue-100 text-xs md:text-sm mt-1 max-w-xl">
                Pantau proses pembelajaran akademik, data pengajar, jadwal kelas, e-rapor, serta arus kas pembayaran SPP secara real-time.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap items-center gap-2">
            <a href="{{ route('web.pembayaran.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
                <span>➕</span> Catat SPP
            </a>
            <a href="{{ route('web.nilai.create') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5">
                <span>📝</span> Input Nilai
            </a>
        </div>
    </div>

    <!-- Core Metrics Stats Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Peserta Didik Aktif</span>
                <div class="text-2xl font-black text-slate-900">{{ $totalSiswa }} Siswa</div>
                <p class="text-[11px] text-slate-500 mt-1">Tersebar di {{ $totalKelas }} Kelas</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                👨‍🎓
            </div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Dewan Guru &amp; Pengajar</span>
                <div class="text-2xl font-black text-slate-900">{{ $totalGuru }} Guru</div>
                <p class="text-[11px] text-slate-500 mt-1">{{ $totalMapel }} Mata Pelajaran</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                👨‍🏫
            </div>
        </div>

        <!-- Total Pemasukan SPP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Kas SPP</span>
                <div class="text-2xl font-black text-emerald-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                <p class="text-[11px] text-slate-500 mt-1">Bulan Ini: Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                💰
            </div>
        </div>

        <!-- Presensi Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Kehadiran Hari Ini</span>
                <div class="text-2xl font-black text-slate-900">{{ $presensiHariIni['hadir'] }} Hadir</div>
                <p class="text-[11px] text-slate-500 mt-1">{{ $presensiHariIni['izin'] + $presensiHariIni['sakit'] }} Izin/Sakit &bull; {{ $presensiHariIni['alpa'] }} Alpa</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                📋
            </div>
        </div>
    </div>

    <!-- Academic & Financial Widgets (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Financial Revenue Trends Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Tren Penerimaan Iuran SPP (Tahun {{ $currentYear }})</h2>
                    <p class="text-xs text-slate-500">Statistik transaksi pembayaran SPP sekolah bulanan</p>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 uppercase tracking-wider">
                    Kas Bulanan
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Today's Class Schedule Widget -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-bold text-slate-900 text-sm">Jadwal Hari {{ $hariIni }}</h2>
                        <p class="text-[11px] text-slate-400">Sesi pelajaran aktif</p>
                    </div>
                    <a href="{{ route('web.jadwal.index') }}" class="text-xs text-blue-600 hover:underline font-bold">Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($jadwalHariIni as $j)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $j->mapel?->nama_mapel }}</span>
                                <span class="text-[11px] text-slate-500">{{ $j->kelas?->nama_kelas }} &bull; {{ $j->guru?->nama_guru }}</span>
                            </div>
                            <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 font-mono text-[10px] font-bold text-blue-700">
                                {{ substr($j->jam_mulai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada jadwal pelajaran yang tercatat untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('web.presensi.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                    Buka Formulir Presensi Kehadiran Siswa &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Transactions & Class Distribution (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Transaksi Pembayaran SPP Terbaru</h2>
                    <p class="text-xs text-slate-400">6 transaksi pembayaran terakhir yang berhasil diverifikasi</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">No. Kwitansi</th>
                            <th class="py-3 px-4 font-semibold">Nama Siswa</th>
                            <th class="py-3 px-4 font-semibold">Periode SPP</th>
                            <th class="py-3 px-4 font-semibold">Nominal</th>
                            <th class="py-3 px-4 font-semibold text-right">Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaru as $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-700">
                                    KWT-{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $t->siswa?->nama }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $t->siswa?->kelas?->nama_kelas }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $t->bulan_dibayar }} {{ $t->tahun_dibayar }}
                                </td>
                                <td class="py-3 px-4 font-bold text-emerald-700">
                                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('web.pembayaran.cetak', $t->id) }}" target="_blank"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        <span>🖨️</span> Cetak
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
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-sm mb-1">Distribusi Siswa per Kelas</h2>
                <p class="text-xs text-slate-400 mb-4">Proporsi siswa aktif di setiap rombongan belajar</p>
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
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#2563eb'
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
                    '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#14b8a6', '#06b6d4'
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
