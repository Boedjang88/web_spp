<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran UKT - {{ $pembayaran->nomor_kuitansi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 md:p-8">
    <div class="max-w-3xl mx-auto bg-white border border-slate-300 rounded-lg p-8 shadow-sm print:border-none print:shadow-none print:p-0">
        <!-- Action Toolbar -->
        <div class="no-print mb-6 pb-4 border-b border-slate-200 flex justify-between items-center">
            <a href="javascript:history.back()" class="text-sm font-medium text-slate-600 hover:text-slate-900 inline-flex items-center">
                &larr; Kembali ke Portal
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm transition">
                Cetak Dokumen
            </button>
        </div>

        <!-- Official Header -->
        <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-950 uppercase">Universitas Teknologi & Sains Nusantara</h1>
                <p class="text-xs text-slate-600 font-medium">Biro Administrasi Akademik & Kemahasiswaan (BAAK) - Bagian Keuangan</p>
                <p class="text-xs text-slate-500 mt-0.5">Jl. Kampus Terpadu No. 1, Graha Rektorat Lt. 2, Nusantara</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-xs font-bold uppercase tracking-wider rounded">
                    Kwitansi Resmi
                </span>
                <p class="text-xs font-mono text-slate-700 mt-1">No: {{ $pembayaran->nomor_kuitansi }}</p>
            </div>
        </div>

        <!-- Payment Details Grid -->
        <div class="space-y-4 text-sm mb-6">
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Telah Diterima Dari</span>
                <span class="col-span-2 font-semibold text-slate-900">{{ $pembayaran->mahasiswa?->nama }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Nomor Induk Mahasiswa (NIM)</span>
                <span class="col-span-2 font-mono font-bold text-slate-900">{{ $pembayaran->mahasiswa?->nim }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Program Studi / Fakultas</span>
                <span class="col-span-2 text-slate-800">{{ $pembayaran->mahasiswa?->prodi?->nama_prodi ?? 'Teknik Informatika' }} ({{ $pembayaran->mahasiswa?->prodi?->fakultas?->nama_fakultas ?? 'FTI' }})</span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Uang Sejumlah</span>
                <span class="col-span-2 font-semibold text-indigo-900 italic bg-indigo-50/60 p-2 rounded">
                    "{{ ucfirst($terbilang) }}"
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Untuk Pembayaran</span>
                <span class="col-span-2 text-slate-800">
                    Pelunasan Uang Kuliah Tunggal (UKT) Semester {{ $pembayaran->semester_dibayar }} Tahun {{ $pembayaran->tahun_dibayar }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Kanal / Bank Transaksi</span>
                <span class="col-span-2 text-slate-800 font-mono">{{ $pembayaran->channel_bayar }} - {{ $pembayaran->kode_bank }} (Ref: {{ $pembayaran->nomor_transaksi_bank ?? '-' }})</span>
            </div>
        </div>

        <!-- Total Amount Display -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between my-6">
            <span class="font-bold text-slate-700 text-sm">JUMLAH DITERIMA:</span>
            <span class="text-xl font-mono font-bold text-slate-950">
                Rp {{ number_format($pembayaran->jumlah_bayar, 2, ',', '.') }}
            </span>
        </div>

        <!-- Signature and QR Validation Section -->
        <div class="mt-8 pt-4 border-t border-slate-200 grid grid-cols-2 gap-8 items-end">
            <!-- QR Code Security Badge -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded text-xs space-y-1">
                <div class="font-semibold text-slate-800 flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Verifikasi Digital Keuangan
                </div>
                <p class="text-slate-500 font-mono break-all text-[10px]">
                    HASH: {{ hash('sha256', $pembayaran->nomor_kuitansi . '|' . $pembayaran->jumlah_bayar) }}
                </p>
                <p class="text-slate-500 text-[10px]">Dokumen ini sah dan diterbitkan secara digital oleh SIAKAD Enterprise.</p>
            </div>

            <!-- Signature Line -->
            <div class="text-right text-xs">
                <p class="text-slate-600">Ditetapkan di Nusantara, {{ $pembayaran->tgl_bayar->format('d F Y') }}</p>
                <p class="text-slate-700 font-medium mt-1">Bendahara Penerimaan & BAAK</p>
                <div class="h-16 flex items-center justify-end pr-4">
                    <span class="font-mono text-xs text-indigo-700 font-semibold border border-indigo-200 bg-indigo-50 px-2 py-0.5 rounded">
                        [DIGITALLY SIGNED & VERIFIED]
                    </span>
                </div>
                <p class="font-bold text-slate-900 underline">{{ $pembayaran->user?->name ?? 'Bagian Keuangan BAAK' }}</p>
                <p class="text-slate-500">NIP. 198203152008121002</p>
            </div>
        </div>
    </div>
</body>
</html>
