<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Ujian - {{ $siswa->nama }} (NISN: {{ $siswa->nisn }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 2px solid #334155 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="no-print max-w-2xl w-full mb-5 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ url()->previous() ?: route('dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Kembali
        </a>
        @if($isLunas)
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></span> Cetak Kartu Ujian (PDF)
            </button>
        @endif
    </div>

    @if(!$isLunas)
        <!-- SPP Clearance Locked Notice -->
        <div class="max-w-2xl w-full bg-white rounded-3xl p-8 border border-rose-200 shadow-sm text-center space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center text-3xl mx-auto border border-rose-200">
                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                    Syarat Keuangan Belum Terpenuhi
                </span>
                <h1 class="text-xl font-black text-slate-900 mt-2">Kartu Ujian Belum Dapat Dicetak</h1>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                    Sesuai ketentuan akademik, kartu peserta ujian hanya dapat diterbitkan setelah seluruh kewajiban administrasi SPP berstatus <strong class="text-emerald-700">LUNAS</strong>.
                </p>
            </div>

            <!-- Detail Tunggakan -->
            <div class="bg-rose-50/60 p-4 rounded-2xl border border-rose-200/80 max-w-md mx-auto text-left text-xs space-y-2">
                <div class="flex justify-between items-center text-rose-900 font-bold border-b border-rose-200/60 pb-2">
                    <span>Total Tunggakan:</span>
                    <span>Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }} ({{ $tunggakan['total_bulan'] }} Bulan)</span>
                </div>
                <div class="text-[11px] text-rose-700">
                    Bulan belum terbayar: <span class="font-semibold">{{ implode(', ', $tunggakan['list_bulan']) }}</span>
                </div>
            </div>

            <div class="pt-2 flex justify-center gap-3">
                <a href="{{ route('web.pembayaran.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Bayar Tagihan SPP Sekarang
                </a>
            </div>
        </div>
    @else
        <!-- Official Printable Exam Pass Card -->
        <div class="print-card max-w-2xl w-full bg-white rounded-3xl border-2 border-slate-800 shadow-md p-6 sm:p-8 space-y-5 relative overflow-hidden">
            
            <!-- Watermark -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none text-9xl font-black select-none">
                SMK
            </div>

            <!-- Kop Kartu Ujian -->
            <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-black text-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</h2>
                        <h1 class="text-base font-black text-slate-900">SMK MERDEKA BELAJAR</h1>
                        <p class="text-[10px] text-slate-500">Jl. Pendidikan No. 45, Bandung &bull; Telp: (022) 7654321</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[10px] font-bold uppercase tracking-wider block">
                        STATUS: LUNAS
                    </span>
                    <span class="text-[10px] font-mono text-slate-400 mt-0.5 block">T.A. 2025/2026</span>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center py-1 bg-slate-50 border border-slate-200 rounded-xl">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">KARTU PESERTA UJIAN AKHIR SEMESTER (UAS)</h3>
            </div>

            <!-- Biodata & Photo Grid -->
            <div class="grid grid-cols-12 gap-4 items-center">
                <!-- Foto Box -->
                <div class="col-span-4 sm:col-span-3">
                    <div class="w-full aspect-[3/4] bg-slate-100 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center text-center p-2 text-slate-400">
                        <span class="text-2xl mb-1"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                        <span class="text-[9px] font-semibold">PAS FOTO<br>3 x 4 CM</span>
                    </div>
                </div>

                <!-- Info Identitas -->
                <div class="col-span-8 sm:col-span-9 text-xs space-y-1.5">
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">Nomor Ujian</span>
                        <span class="col-span-8 font-bold font-mono text-slate-900">: UAS-{{ $siswa->nisn }}-2025</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">Nama Lengkap</span>
                        <span class="col-span-8 font-black text-slate-900 text-sm">: {{ $siswa->nama }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">NISN / NIS</span>
                        <span class="col-span-8 font-mono text-slate-700">: {{ $siswa->nisn }} / {{ $siswa->nis }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">Kelas</span>
                        <span class="col-span-8 font-bold text-slate-900">: {{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">Kompetensi</span>
                        <span class="col-span-8 text-slate-700">: {{ $siswa->kelas->kompetensi_keahlian ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-4 text-slate-400 font-semibold">Status SPP</span>
                        <span class="col-span-8 font-bold text-emerald-700">: Lunas Administrasi (Bebas Tanggungan)</span>
                    </div>
                </div>
            </div>

            <!-- Tata Tertib Singkat -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[10px] text-slate-600 space-y-1">
                <span class="font-bold text-slate-900 block text-[11px]">Tata Tertib Peserta Ujian:</span>
                <ol class="list-decimal list-inside space-y-0.5 text-slate-500">
                    <li>Peserta wajib membawa dan menunjukkan Kartu Peserta Ujian ini di setiap sesi.</li>
                    <li>Hadir di ruang ujian paling lambat 15 menit sebelum waktu pengerjaan dimulai.</li>
                    <li>Dilarang membawa alat komunikasi, contekan, atau bekerja sama selama ujian berlangsung.</li>
                </ol>
            </div>

            <!-- Tanda Tangan & QR Verification -->
            <div class="grid grid-cols-2 pt-3 border-t border-slate-200 text-xs">
                <div class="space-y-1">
                    <div class="font-mono text-[9px] text-slate-400">Verifikasi Digital:</div>
                    <div class="inline-block p-1.5 bg-slate-100 rounded-lg border border-slate-200 font-mono text-[9px] text-slate-600">
                        [QR: VERIFIED-LUNAS-{{ substr(md5($siswa->nisn), 0, 10) }}]
                    </div>
                </div>
                <div class="text-right space-y-12">
                    <div>
                        <span class="text-[11px] text-slate-500 block">Bandung, {{ now()->format('d F Y') }}</span>
                        <span class="text-[11px] font-bold text-slate-800 block">Dekan Fakultas / Panitia UTS/UAS</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 underline block">Drs. H. Mulyadi, M.M.</span>
                        <span class="text-[10px] text-slate-500 font-mono block">NIP. 197008151995121001</span>
                    </div>
                </div>
            </div>

        </div>
    @endif

</body>
</html>
