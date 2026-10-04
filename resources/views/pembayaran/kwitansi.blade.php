<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->siswa?->nama ?? 'SPP' }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .receipt-box {
            border: 2px solid #1e293b;
            padding: 24px;
            border-radius: 8px;
            max-width: 700px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .school-address {
            font-size: 11px;
            color: #64748b;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 16px;
            color: #2563eb;
        }
        .doc-title span {
            font-size: 11px;
            color: #475569;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .content-table td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .content-table td.label {
            width: 25%;
            color: #475569;
        }
        .content-table td.separator {
            width: 2%;
        }
        .content-table td.value {
            font-weight: 600;
        }
        .amount-box {
            background-color: #f1f5f9;
            border: 1px dashed #94a3b8;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .amount-text {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .terbilang {
            font-size: 11px;
            font-style: italic;
            color: #475569;
            margin-top: 4px;
        }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 12px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-space {
            height: 50px;
        }
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-box {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>

    <!-- Print Control Banner -->
    <div class="no-print" style="max-width: 700px; margin: 0 auto 16px; display: flex; justify-content: space-between; align-items: center;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #334155; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Tutup Jendela
        </button>
    </div>

    <!-- Official Printable Receipt -->
    <div class="receipt-box">
        <div class="header">
            <div>
                <div class="school-name">SMK NEGERI / SWASTA CONTOH</div>
                <div class="school-address">Jl. Pendidikan No. 123, Kota Bandung, Jawa Barat</div>
            </div>
            <div class="doc-title">
                <h2>BUKTI PEMBAYARAN SPP</h2>
                <span>No: KWT-{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <table class="content-table">
            <tr>
                <td class="label">Telah Diterima Dari</td>
                <td class="separator">:</td>
                <td class="value">{{ $pembayaran->siswa?->nama }}</td>
            </tr>
            <tr>
                <td class="label">NISN / NIS</td>
                <td class="separator">:</td>
                <td class="value">{{ $pembayaran->siswa?->nisn }} / {{ $pembayaran->siswa?->nis }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Jurusan</td>
                <td class="separator">:</td>
                <td class="value">{{ $pembayaran->siswa?->kelas?->nama_kelas }} ({{ $pembayaran->siswa?->kelas?->kompetensi_keahlian }})</td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="separator">:</td>
                <td class="value">SPP Bulan {{ $pembayaran->bulan_dibayar }} Tahun {{ $pembayaran->tahun_dibayar }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="separator">:</td>
                <td class="value">{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <div class="amount-box">
            <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: bold;">Jumlah Diterima:</div>
            <div class="amount-text">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</div>
            <div class="terbilang">Terbilang: # {{ ucfirst($terbilang) }} #</div>
        </div>

        <div class="footer-signatures">
            <div class="signature-box">
                <div>Siswa / Pembayar</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $pembayaran->siswa?->nama }}</div>
            </div>
            <div class="signature-box">
                <div>Bandung, {{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->translatedFormat('d F Y') }}</div>
                <div>Petugas Loket Pembayaran</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $pembayaran->petugas?->name ?? 'Petugas SPP' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
