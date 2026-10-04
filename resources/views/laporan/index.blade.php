@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi SPP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Rekapitulasi & Laporan Keuangan SPP</h1>
            <p class="text-xs text-slate-500 mt-0.5">Filter transaksi, cetak rekapitulasi resmi, dan ekspor ke format CSV / Excel</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.laporan.exportCsv', request()->query()) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>📥</span> Export CSV / Excel
            </a>
            <a href="{{ route('web.laporan.cetak', request()->query()) }}" target="_blank" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>🖨️</span> Cetak Laporan (Print / PDF)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('web.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Dari Tanggal -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Sampai Tanggal -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filter Kelas -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Filter Kelas</label>
                <select name="id_kelas" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Petugas -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Filter Petugas</label>
                <select name="id_petugas" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ request('id_petugas') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-1.5 px-4 bg-slate-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['start_date', 'end_date', 'id_kelas', 'id_petugas']))
                    <a href="{{ route('web.laporan.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-lg">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Highlights -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider block">Total Pemasukan (Sesuai Filter)</span>
                <div class="text-2xl font-black mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
            <span class="text-3xl p-3 bg-white/10 rounded-2xl">💰</span>
        </div>

        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-blue-100 uppercase tracking-wider block">Volume Transaksi Tercatat</span>
                <div class="text-2xl font-black mt-1">{{ $totalTransaksi }} Transaksi</div>
            </div>
            <span class="text-3xl p-3 bg-white/10 rounded-2xl">📑</span>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">No</th>
                        <th class="py-3 px-4 font-semibold">No. Bukti</th>
                        <th class="py-3 px-4 font-semibold">Tgl Transaksi</th>
                        <th class="py-3 px-4 font-semibold">Siswa</th>
                        <th class="py-3 px-4 font-semibold">Kelas</th>
                        <th class="py-3 px-4 font-semibold">Periode SPP</th>
                        <th class="py-3 px-4 font-semibold">Petugas</th>
                        <th class="py-3 px-4 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">KWT-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->tgl_bayar }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->siswa?->nama }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->siswa?->kelas?->nama_kelas }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->petugas?->name }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-600 text-right">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi yang cocok dengan kriteria filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
