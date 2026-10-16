@extends('layouts.app')

@section('title', 'Detail Pembayaran #' . $pembayaran->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Header & Action -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Transaksi Pembayaran</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">#{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Bukti Transaksi Pembayaran</h1>
            <p class="text-xs text-slate-500">Rincian tanda terima pembayaran SPP / UKT mahasiswa</p>
        </div>
        <div class="flex items-center gap-2">
            @if(!empty($waLink))
            <a href="{{ $waLink }}" target="_blank"
                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Kirim WA</span>
            </a>
            @else
            <span class="px-3.5 py-2 bg-slate-100 text-slate-400 rounded-xl text-xs font-medium inline-flex items-center gap-1.5 cursor-not-allowed border border-slate-200" title="Nomor WhatsApp tidak tersedia">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Kirim WA</span>
            </span>
            @endif
            <a href="{{ route('web.pembayaran.cetak', $pembayaran->id) }}" target="_blank"
                class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-indigo-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kwitansi</span>
            </a>
        </div>
    </div>

    <!-- Receipt Card Preview -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-5">
        
        <!-- Header Kwitansi -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block">Kwitansi Resmi SPP / UKT</span>
                <span class="text-lg font-black text-slate-900">No. KWT-{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>LUNAS</span>
            </span>
        </div>

        <!-- Detail Rincian -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">Nama Mahasiswa</span>
                <span class="font-bold text-slate-900 text-sm">{{ $pembayaran->siswa?->nama ?? '-' }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">NIM/NISN &bull; Kelas</span>
                <span class="font-semibold text-slate-800">{{ $pembayaran->siswa?->nisn }} ({{ $pembayaran->siswa?->kelas?->nama_kelas ?? '-' }})</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">Periode Pembayaran</span>
                <span class="font-bold text-indigo-700">UKT Bulan {{ $pembayaran->bulan_dibayar }} {{ $pembayaran->tahun_dibayar }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">Tanggal Transaksi</span>
                <span class="font-semibold text-slate-800">{{ $pembayaran->tgl_bayar }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">Petugas Penerima</span>
                <span class="font-semibold text-slate-800">{{ $pembayaran->petugas?->name ?? 'Petugas Kasir BAK' }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[11px] mb-0.5">Tarif UKT Terkait</span>
                <span class="font-semibold text-slate-800">Tahun Anggaran {{ $pembayaran->spp?->tahun }}</span>
            </div>
        </div>

        <!-- Nominal Highlight -->
        <div class="p-4 bg-gradient-to-r from-slate-50 to-indigo-50/40 border border-slate-200 rounded-2xl space-y-1">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Pembayaran:</span>
            <div class="text-2xl font-black text-slate-900">
                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 italic">Terbilang: {{ ucfirst($terbilang) }}</p>
        </div>

        <!-- Action Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <a href="{{ route('web.pembayaran.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">&larr; Kembali ke Daftar</a>
            <form action="{{ route('web.pembayaran.destroy', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi pembayaran ini? Saldo tagihan mahasiswa akan dikembalikan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition inline-flex items-center gap-1 border border-rose-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Batalkan Transaksi</span>
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
