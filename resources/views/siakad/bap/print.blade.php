<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Perkuliahan (BAP) - Pertemuan {{ $bap->pertemuan_ke }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 2px solid #1e293b !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <div class="no-print max-w-3xl w-full mb-5 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
            <span>🖨️</span> Cetak BAP (PDF)
        </button>
    </div>

    <div class="print-card max-w-3xl w-full bg-white rounded-3xl border-2 border-slate-800 shadow-md p-8 sm:p-10 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
            <div>
                <h1 class="text-base font-black text-slate-900 uppercase">BERITA ACARA PERKULIAHAN (BAP) DIGITAL</h1>
                <p class="text-xs text-slate-500">Sistem Informasi Akademik Terpadu (SIAKAD Enterprise)</p>
            </div>
            <div class="text-right">
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-bold font-mono">
                    PERTEMUAN KE-{{ $bap->pertemuan_ke }}
                </span>
            </div>
        </div>

        <!-- Meta Perkuliahan -->
        <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div>
                <span class="text-slate-400 block text-[11px]">Mata Kuliah</span>
                <span class="font-bold text-slate-900">{{ $bap->kelasKuliah?->mataKuliah?->nama_mk }} ({{ $bap->kelasKuliah?->mataKuliah?->kode_mk }})</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Kelas & Ruangan</span>
                <span class="font-bold text-slate-900">Kelas {{ $bap->kelasKuliah?->nama_kelas }} &bull; {{ $bap->ruangan?->nama_ruangan }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Dosen Pengampu</span>
                <span class="font-bold text-slate-900">{{ $bap->dosen?->nama_guru }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Waktu Pelaksanaan</span>
                <span class="font-bold text-slate-900">{{ $bap->tanggal_pelaksanaan?->format('d/m/Y') }} &bull; {{ substr($bap->jam_mulai_real, 0, 5) }} - {{ substr($bap->jam_selesai_real, 0, 5) }} WIB</span>
            </div>
        </div>

        <!-- Materi Pembahasan -->
        <div class="space-y-2 text-xs">
            <h3 class="font-bold text-slate-900 text-xs">Materi Perkuliahan yang Disampaikan:</h3>
            <div class="p-3.5 bg-white border border-slate-200 rounded-xl text-slate-700 leading-relaxed font-mono text-[11px]">
                {{ $bap->materi_pembahasan }}
            </div>
        </div>

        <!-- Statistik Kehadiran -->
        <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                <span class="text-[11px] block">Mahasiswa Hadir (Geo-fenced Scan)</span>
                <span class="text-lg font-black">{{ $bap->total_mahasiswa_hadir }} Mahasiswa</span>
            </div>
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800">
                <span class="text-[11px] block">Mahasiswa Tidak Hadir / Alpa</span>
                <span class="text-lg font-black">{{ $bap->total_mahasiswa_absen }} Mahasiswa</span>
            </div>
        </div>

        <!-- Tanda Tangan & Hash Verifikasi -->
        <div class="grid grid-cols-2 pt-6 border-t border-slate-200 text-xs items-end">
            <div class="space-y-1">
                <span class="text-[10px] text-slate-400 font-mono block">Digital Signature Integrity Hash:</span>
                <code class="text-[9px] text-slate-600 bg-slate-100 p-1.5 rounded-lg block break-all font-mono">
                    {{ $bap->digital_signature_hash ?? hash('sha256', $bap->id . '-' . $bap->tanggal_pelaksanaan) }}
                </code>
            </div>
            <div class="text-right space-y-12">
                <div>
                    <span class="text-[11px] text-slate-500 block">Dosen Pengampu Mata Kuliah,</span>
                </div>
                <div>
                    <span class="font-bold text-slate-900 underline block">{{ $bap->dosen?->nama_guru }}</span>
                    <span class="font-mono text-[10px] text-slate-500 block">NIP. {{ $bap->dosen?->nip ?? '-' }}</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
