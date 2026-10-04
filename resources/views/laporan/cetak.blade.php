<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pembayaran SPP</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #334155;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .school-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 13px;
            font-weight: bold;
            margin-top: 4px;
            color: #1e3a8a;
        }
        .meta-info {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-box {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 12px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .sig-col {
            width: 220px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: bold;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 16px; display: flex; justify-content: space-between;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak / Ekspor PDF
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #334155; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Tutup
        </button>
    </div>

    <!-- Official Header -->
    <div class="header">
        <div class="school-title">SMK NEGERI / SWASTA CONTOH</div>
        <div class="meta-info">Jl. Pendidikan No. 123, Bandung, Jawa Barat | Telp: (022) 1234567 | Website: www.sekolah.sch.id</div>
        <div class="report-subtitle">LAPORAN REKAPITULASI PEMBAYARAN SPP</div>
        <div class="meta-info">
            Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }} 
            @if($filterKelas) | Kelas: {{ $filterKelas->nama_kelas }} @endif
        </div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>No. Kwitansi</th>
                <th>Tanggal</th>
                <th>NISN / Nama Siswa</th>
                <th>Kelas</th>
                <th>Periode SPP</th>
                <th>Petugas</th>
                <th class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayarans as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">KWT-{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $p->tgl_bayar }}</td>
                    <td>
                        <strong>{{ $p->siswa?->nama }}</strong>
                        <div style="font-size: 9px; color: #64748b;">NISN: {{ $p->siswa?->nisn }}</div>
                    </td>
                    <td>{{ $p->siswa?->kelas?->nama_kelas }}</td>
                    <td>{{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}</td>
                    <td>{{ $p->petugas?->name }}</td>
                    <td class="text-right font-bold">{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="total-box">
                <td colspan="7" class="text-right">TOTAL PENERIMAAN KESELURUHAN:</td>
                <td class="text-right" style="color: #1e3a8a;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 11px; margin-bottom: 20px;">
        <em>Terbilang: # {{ ucfirst($terbilang) }} #</em>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-col">
            <div>Mengetahui,</div>
            <div>Kepala Sekolah</div>
            <div class="sig-space"></div>
            <div class="sig-name">Dr. H. Hendra Wijaya, M.Pd.</div>
            <div style="font-size: 9px; color: #64748b;">NIP. 197508152000031001</div>
        </div>

        <div class="sig-col">
            <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Bendahara / Petugas SPP</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ auth()->user()->name ?? 'Administrator SPP' }}</div>
            <div style="font-size: 9px; color: #64748b;">Petugas Loket Terverifikasi</div>
        </div>
    </div>

</body>
</html>
