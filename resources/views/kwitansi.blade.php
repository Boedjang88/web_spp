<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->no_transaksi ?? 'SPP' }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font ala mesin ketik */
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h2, .header h4 { margin: 5px 0; }
        .content { margin-bottom: 20px; }
        .row { display: flex; margin-bottom: 8px; }
        .label { width: 180px; font-weight: bold; }
        .separator { width: 20px; }
        .value { flex: 1; }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .signature { width: 200px; }
        .line { border-bottom: 1px solid #000; margin-top: 50px; }
        
        /* Tombol Print (Hilang pas diprint) */
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #333;
            color: #fff;
            text-align: center;
            text-decoration: none;
            margin-bottom: 20px;
            font-family: sans-serif;
        }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
            .container { border: none; }
        }
    </style>
</head>
<body>

    <a href="#" onclick="window.print()" class="btn-print">🖨️ KLIK UNTUK MENCETAK</a>

    <div class="container">
        <div class="header">
            <h2>SMK MERDEKA BELAJAR</h2>
            <h4>BUKTI PEMBAYARAN SPP</h4>
            <small>Jl. Pendidikan No. 1, Bekasi | Telp: 021-12345678</small>
        </div>

        <div class="content">
            <div class="row">
                <div class="label">Tanggal Bayar</div>
                <div class="separator">:</div>
                <div class="value">{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->translatedFormat('d F Y') }}</div>
            </div>
            <div class="row">
                <div class="label">Nama Siswa</div>
                <div class="separator">:</div>
                <div class="value">{{ $pembayaran->siswa->nama }}</div>
            </div>
            <div class="row">
                <div class="label">Kelas / Jurusan</div>
                <div class="separator">:</div>
                <div class="value">
                    {{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }} - 
                    {{ $pembayaran->siswa->kelas->kompetensi_keahlian ?? '-' }}
                </div>
            </div>
            <div class="row">
                <div class="label">Pembayaran Bulan</div>
                <div class="separator">:</div>
                <div class="value">{{ $pembayaran->bulan_dibayar }} {{ $pembayaran->tahun_dibayar }}</div>
            </div>
            <div class="row">
                <div class="label">Tahun Ajaran SPP</div>
                <div class="separator">:</div>
                <div class="value">{{ $pembayaran->spp->tahun }}</div>
            </div>
            <div class="row">
                <div class="label" style="font-size: 1.2em;">JUMLAH BAYAR</div>
                <div class="separator">:</div>
                <div class="value" style="font-weight: bold; font-size: 1.2em;">
                    Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Siswa / Penyetor</p>
                <div class="line"></div>
                <p>{{ $pembayaran->siswa->nama }}</p>
            </div>
            <div class="signature">
                <p>Petugas Penerima</p>
                <div class="line"></div>
                <p>{{ $pembayaran->petugas->name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>

    <script>
        // Otomatis munculin dialog print pas halaman dibuka
        window.print();
    </script>
</body>
</html>