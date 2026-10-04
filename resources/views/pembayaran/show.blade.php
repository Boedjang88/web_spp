@extends('layouts.app')

@section('title', 'Detail Pembayaran #' . $pembayaran->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header & Action -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Riwayat Pembayaran</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">#{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Bukti Transaksi Pembayaran</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $waLink }}" target="_blank"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>📱</span> Kirim Bukti via WA
            </a>
            <a href="{{ route('web.pembayaran.cetak', $pembayaran->id) }}" target="_blank"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>🖨️</span> Cetak Kwitansi
            </a>
        </div>
    </div>

    <!-- Receipt Card Preview -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
        
        <!-- Header Kwitansi -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 block">Kwitansi Pembayaran SPP</span>
                <span class="text-base font-bold text-slate-900">No. KWT-{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                LUNAS
            </span>
        </div>

        <!-- Detail Rincian -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Nama Siswa</span>
                <span class="font-bold text-slate-800 text-sm">{{ $pembayaran->siswa?->nama ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">NISN / Kelas</span>
                <span class="font-medium text-slate-800">{{ $pembayaran->siswa?->nisn }} ({{ $pembayaran->siswa?->kelas?->nama_kelas }})</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Untuk Pembayaran</span>
                <span class="font-bold text-blue-700">SPP Bulan {{ $pembayaran->bulan_dibayar }} {{ $pembayaran->tahun_dibayar }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Tanggal Transaksi</span>
                <span class="font-medium text-slate-800">{{ $pembayaran->tgl_bayar }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Petugas Penerima</span>
                <span class="font-medium text-slate-800">{{ $pembayaran->petugas?->name ?? 'Petugas Loket' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Tarif SPP Terkait</span>
                <span class="font-medium text-slate-800">Tahun {{ $pembayaran->spp?->tahun }}</span>
            </div>
        </div>

        <!-- Nominal Highlight -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Pembayaran:</span>
            <div class="text-2xl font-black text-slate-900">
                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 italic">Terbilang: {{ ucfirst($terbilang) }}</p>
        </div>

        <!-- Action Footer -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Daftar</a>
            <form action="{{ route('web.pembayaran.destroy', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi pembayaran ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-rose-600 hover:underline font-semibold">Batalkan Transaksi Ini</button>
            </form>
        </div>

    </div>

</div>
@endsection
