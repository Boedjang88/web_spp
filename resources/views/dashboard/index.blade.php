@extends('layouts.app')

@section('title', 'Dashboard SIAKAD Enterprise & Keuangan')

@section('content')
<div class="space-y-6">

    <!-- Accessible Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>
        <span>/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold" aria-current="page">Ringkasan Eksekutif</span>
    </nav>

    @php
        $hour = now()->hour;
        $greeting = match(true) {
            $hour < 11 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
        $roleName = match(auth()->user()->role ?? '') {
            'admin', 'superadmin' => 'Administrator Sistem',
            'petugas' => 'Petugas Keuangan & TU',
            'wali', 'parent' => 'Wali Mahasiswa',
            'siswa', 'mahasiswa' => 'Mahasiswa Aktif',
            'guru', 'dosen' => 'Dosen Pengajar',
            default => ucfirst(auth()->user()->role ?? 'Pengguna')
        };
    @endphp

    <!-- Executive Header Banner (Calm, Non-glaring Matte Workspace Surface) -->
    <div class="bg-white dark:bg-slate-800/80 rounded-2xl p-5 sm:p-6 md:p-7 border border-slate-200/80 dark:border-slate-700/60 shadow-xs transition-colors">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
            <div class="flex items-start sm:items-center gap-4">
                <!-- User Avatar Badge -->
                <div class="relative shrink-0">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-brand-600 text-white font-bold text-xl flex items-center justify-center shadow-xs overflow-hidden">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full" title="Sistem Aktif"></span>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2 font-sans">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-600/60">
                            Sistem Akademik &amp; Keuangan Terpadu
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/60 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            {{ $roleName }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            T.A. 2025/2026
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                        {{ $greeting }}, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Ringkasan real-time aktivitas akademik, presensi mahasiswa, jadwal perkuliahan, dan arus kas pembayaran UKT.
                    </p>
                </div>
            </div>

            <!-- Quick Action Primary Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto shrink-0">
                <a href="{{ route('web.pembayaran.create') }}" class="flex-1 md:flex-none justify-center bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4.5 py-2.5 rounded-xl text-xs shadow-xs hover:shadow-sm transition inline-flex items-center gap-2 min-h-9.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat UKT</span>
                </a>
                <a href="{{ route('web.nilai.create') }}" class="flex-1 md:flex-none justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4.5 py-2.5 rounded-xl text-xs transition inline-flex items-center gap-2 border border-slate-200 dark:border-slate-600 min-h-9.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Input Nilai</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Core Metrics Stats Cards (4 Columns with Soothing Palette) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Mahasiswa -->
        <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mahasiswa Aktif</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-200/60 dark:border-blue-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 font-mono tracking-tight">{{ $totalSiswa }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                <span>Tersebar di {{ $totalKelas }} Kelas Kuliah</span>
            </div>
        </div>

        <!-- Total Dosen -->
        <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dosen Pengajar</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200/60 dark:border-indigo-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 font-mono tracking-tight">{{ $totalGuru }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                <span>Mengampu {{ $totalMapel }} Mata Kuliah</span>
            </div>
        </div>

        <!-- Total Pemasukan UKT -->
        <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Kas UKT</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200/60 dark:border-emerald-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Bulan Ini: Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Presensi Hari Ini -->
        <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kehadiran Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-200/60 dark:border-amber-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 font-mono tracking-tight">{{ $presensiHariIni['hadir'] }} Sesi</div>
            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>{{ $presensiHariIni['izin'] + $presensiHariIni['sakit'] }} Izin/Sakit &bull; {{ $presensiHariIni['alpa'] }} Alpa</span>
            </div>
        </div>
    </div>

    <!-- Academic & Financial Widgets (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Financial Revenue Trends Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Tren Penerimaan Kas UKT ({{ $currentYear }})</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Statistik penerimaan kas UKT bulanan</p>
                </div>
                <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/70 dark:border-blue-800/60">
                    REALTIME LEDGER
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Today's Class Schedule Widget -->
        <div class="bg-white dark:bg-slate-800/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Jadwal Kuliah {{ $hariIni }}</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Sesi perkuliahan aktif</p>
                    </div>
                    <a href="{{ route('web.jadwal.index') }}" class="text-[11px] text-brand-600 dark:text-brand-400 font-bold hover:underline inline-flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($jadwalHariIni as $j)
                        <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 hover:border-slate-300 dark:hover:border-slate-600 transition flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs truncate">{{ $j->mapel?->nama_mapel }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block truncate">{{ $j->kelas?->nama_kelas }} &bull; {{ $j->guru?->nama_guru }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[10px] font-bold text-brand-700 dark:text-brand-300 shadow-2xs shrink-0">
                                {{ substr($j->jam_mulai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs font-medium">
                            Tidak ada jadwal perkuliahan untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-center">
                <a href="{{ route('web.presensi.index') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline inline-flex items-center gap-1">
                    <span>Kelola Presensi Kehadiran</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Transactions & Class Distribution (Grid 2:1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Recent Transactions -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider">Pembayaran UKT Terakhir</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">6 transaksi pembayaran UKT terverifikasi</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline inline-flex items-center gap-1">
                    <span>Semua Transaksi</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-5">No. Kwitansi</th>
                            <th class="py-3 px-5">Mahasiswa</th>
                            <th class="py-3 px-5">Periode</th>
                            <th class="py-3 px-5">Nominal</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-mono">
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
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-xl border border-blue-200/70 dark:border-blue-800/60 text-xs font-semibold transition inline-flex items-center gap-1 shadow-2xs">
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
        <div class="bg-white dark:bg-slate-800/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wider mb-1">Distribusi Mahasiswa per Kelas</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4">Proporsi mahasiswa di setiap kelas kuliah</p>
                <div class="h-48 flex items-center justify-center">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-center">
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
    const initTextColor = isInitialDark ? '#94a3b8' : '#475569';
    const initCardBorder = isInitialDark ? '#1e293b' : '#ffffff';

    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan UKT (Rp)',
                data: @json($chartData),
                borderColor: '#1a73e8',
                backgroundColor: isInitialDark ? 'rgba(26, 115, 232, 0.15)' : 'rgba(26, 115, 232, 0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 3.5,
                pointHoverRadius: 5.5,
                pointBackgroundColor: '#1a73e8',
                pointBorderColor: '#ffffff'
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
                    '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#f43f5e', '#06b6d4', '#6366f1', '#14b8a6', '#ec4899', '#84cc16', '#0284c7', '#d97706'
                ],
                borderWidth: 2,
                borderColor: initCardBorder
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
        const textColor = isDark ? '#94a3b8' : '#475569';
        const cardBgColor = isDark ? '#1e293b' : '#ffffff';

        if (revenueChart && revenueChart.options.scales.y) {
            revenueChart.options.scales.y.grid.color = gridColor;
            revenueChart.options.scales.y.ticks.color = textColor;
            revenueChart.options.scales.x.ticks.color = textColor;
            revenueChart.data.datasets[0].backgroundColor = isDark ? 'rgba(26, 115, 232, 0.15)' : 'rgba(26, 115, 232, 0.08)';
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
