@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header & Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Pembayaran SPP</h1>
            <p class="text-blue-100 text-sm mt-1">Selamat datang di panel manajemen SPP sekolah. Kelola data dan pencatatan transaksi dengan mudah.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.pembayaran.create') }}" class="bg-white hover:bg-blue-50 text-blue-800 font-semibold px-4 py-2.5 rounded-xl text-sm shadow-sm transition inline-flex items-center gap-1.5">
                <span>➕</span> Catat Pembayaran Baru
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pemasukan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Pemasukan</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg text-base">💰</span>
            </div>
            <div class="text-xl font-bold text-slate-900">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 mt-1">Akumulasi seluruh transaksi SPP</p>
        </div>

        <!-- Pemasukan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Pemasukan Hari Ini</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg text-base">📅</span>
            </div>
            <div class="text-xl font-bold text-slate-900">
                Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $transaksiHariIni }} transaksi berhasil dicatat hari ini</p>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Siswa Aktif</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg text-base">👨‍🎓</span>
            </div>
            <div class="text-xl font-bold text-slate-900">
                {{ $totalSiswa }} Siswa
            </div>
            <p class="text-xs text-slate-500 mt-1">Terdaftar di {{ $totalKelas }} kelas</p>
        </div>

        <!-- Tarif SPP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Periode Tarif SPP</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-lg text-base">🏷️</span>
            </div>
            <div class="text-xl font-bold text-slate-900">
                {{ $totalSpp }} Tahun Tarif
            </div>
            <p class="text-xs text-slate-500 mt-1">Pengaturan nominal pembayaran</p>
        </div>
    </div>

    <!-- Analytics Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Revenue Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-base">Tren Pemasukan SPP (Tahun {{ $currentYear }})</h2>
                    <p class="text-xs text-slate-500">Akumulasi penerimaan pembayaran SPP per bulan</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">Grafik Bulanan</span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Student Distribution Doughnut Chart (1 Col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-base mb-1">Distribusi Siswa per Kelas</h2>
                <p class="text-xs text-slate-500 mb-4">Proporsi jumlah siswa aktif per kelas</p>
                <div class="h-52 flex items-center justify-center">
                    <canvas id="classChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 text-center">
                <span class="text-xs text-slate-500">Total: <strong class="text-slate-800">{{ $totalSiswa }} Siswa</strong> di <strong class="text-slate-800">{{ $totalKelas }} Kelas</strong></span>
            </div>
        </div>
    </div>

    <!-- Quick Action & Recent Payments Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Latest Transactions (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-base">Transaksi Pembayaran Terbaru</h2>
                    <p class="text-xs text-slate-500">Daftar transaksi SPP yang baru saja masuk</p>
                </div>
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider">
                            <th class="pb-3 font-semibold">Siswa</th>
                            <th class="pb-3 font-semibold">Periode</th>
                            <th class="pb-3 font-semibold">Tanggal</th>
                            <th class="pb-3 font-semibold">Nominal</th>
                            <th class="pb-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaru as $bayar)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-medium text-slate-900">
                                    {{ $bayar->siswa?->nama ?? '-' }}
                                    <span class="block text-[10px] text-slate-400 font-normal">NISN: {{ $bayar->siswa?->nisn }} ({{ $bayar->siswa?->kelas?->nama_kelas }})</span>
                                </td>
                                <td class="py-3 text-slate-600">
                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">
                                        {{ $bayar->bulan_dibayar }} {{ $bayar->tahun_dibayar }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-600">{{ $bayar->tgl_bayar }}</td>
                                <td class="py-3 font-semibold text-emerald-600">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('web.pembayaran.show', $bayar->id) }}" class="text-blue-600 hover:underline font-semibold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada data transaksi pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access Sidebar -->
        <div class="space-y-4">
            <!-- Shortcut Box -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900 text-sm mb-3">Menu Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('web.pembayaran.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-blue-50 hover:bg-blue-100/80 text-blue-900 transition">
                        <span class="text-xs font-semibold">➕ Entri Pembayaran SPP</span>
                        <span class="text-xs font-bold text-blue-600">&rarr;</span>
                    </a>
                    <a href="{{ route('web.siswa.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100/80 text-indigo-900 transition">
                        <span class="text-xs font-semibold">👨‍🎓 Registrasi Siswa Baru</span>
                        <span class="text-xs font-bold text-indigo-600">&rarr;</span>
                    </a>
                    <a href="{{ route('web.laporan.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 transition">
                        <span class="text-xs font-semibold">📊 Rekap & Export Laporan</span>
                        <span class="text-xs font-bold text-emerald-600">&rarr;</span>
                    </a>
                    <a href="{{ url('/api/docs') }}" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 transition">
                        <span class="text-xs font-semibold">⚡ Interactive API Docs UI</span>
                        <span class="text-xs font-bold text-purple-600">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Public Inquiry Promo -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-5 text-white shadow-sm">
                <h3 class="font-bold text-sm mb-1">Cek Tagihan Publik</h3>
                <p class="text-xs text-slate-300 mb-4">Wali murid atau siswa dapat mengecek status tagihan tanpa login menggunakan nomor NISN.</p>
                <a href="{{ route('cek.index') }}" target="_blank" class="block text-center py-2 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-semibold transition">
                    Buka Halaman Pencarian &rarr;
                </a>
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
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: {!! json_encode($chartData) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                        },
                        font: { size: 10 }
                    },
                    grid: { color: '#f1f5f9' }
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
            labels: {!! json_encode($kelasLabels) !!},
            datasets: [{
                data: {!! json_encode($kelasData) !!},
                backgroundColor: ['#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b'],
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
            },
            cutout: '70%'
        }
    });
</script>
@endpush
@endsection
