@extends('layouts.app')

@section('title', 'Transaksi Pembayaran SPP')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Transaksi Pembayaran SPP</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dan telusuri riwayat transaksi pembayaran SPP</p>
        </div>
        <a href="{{ route('web.pembayaran.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1">
            <span>➕</span> Entri Pembayaran Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('web.pembayaran.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NISN Siswa..."
                    class="w-full sm:max-w-xs px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <select name="bulan_dibayar" class="w-full sm:w-auto px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                        <option value="{{ $bulan }}" {{ request('bulan_dibayar') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                    @endforeach
                </select>

                <input type="number" name="tahun_dibayar" value="{{ request('tahun_dibayar') }}" placeholder="Tahun (cth: 2025)"
                    class="w-full sm:w-32 px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-medium hover:bg-slate-900">Filter</button>
                
                @if(request('search') || request('bulan_dibayar') || request('tahun_dibayar'))
                    <a href="{{ route('web.pembayaran.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">No</th>
                        <th class="py-3 px-4 font-semibold">Siswa & Kelas</th>
                        <th class="py-3 px-4 font-semibold">Periode Pembayaran</th>
                        <th class="py-3 px-4 font-semibold">Tanggal Bayar</th>
                        <th class="py-3 px-4 font-semibold">Petugas Loket</th>
                        <th class="py-3 px-4 font-semibold">Nominal</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                <span class="font-bold text-slate-900 block">{{ $item->siswa?->nama ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">NISN: {{ $item->siswa?->nisn }} ({{ $item->siswa?->kelas?->nama_kelas }})</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">
                                    {{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->tgl_bayar }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->petugas?->name ?? 'Petugas' }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-600">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('web.pembayaran.cetak', $item->id) }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold bg-blue-50 px-2 py-1 rounded">🖨️ Cetak</a>
                                <a href="{{ route('web.pembayaran.show', $item->id) }}" class="text-slate-600 hover:text-slate-900 font-semibold">Detail</a>
                                <form action="{{ route('web.pembayaran.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Batalkan/Hapus pembayaran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Batal</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada riwayat transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
