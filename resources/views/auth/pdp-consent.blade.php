<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pemrosesan Data Pribadi (UU PDP No. 27/2022)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex items-center justify-center">

    <div class="max-w-2xl w-full bg-slate-800 border border-slate-700 rounded-3xl p-6 sm:p-10 shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4 border-b border-slate-700 pb-5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-2xl flex-shrink-0">
                🛡️
            </div>
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    Kepatuhan Regulasi Nasional
                </span>
                <h1 class="text-base font-bold text-white mt-0.5">Persetujuan Pemrosesan Data Pribadi (UU PDP)</h1>
            </div>
        </div>

        <!-- Statement Box -->
        <div class="bg-slate-950/60 rounded-2xl border border-slate-700/80 p-4 text-xs text-slate-300 space-y-3 max-h-64 overflow-y-auto leading-relaxed">
            <p class="font-bold text-white">Berdasarkan Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP):</p>
            <ol class="list-decimal list-inside space-y-2 text-slate-400">
                <li>SIAKAD Enterprise memproses data spesifik (NIK, Nama Ibu Kandung, Nomor Kontak Wali, Rekam Akademik, Nilai, Presensi) untuk keperluan penyelenggaraan Tridharma Perguruan Tinggi dan pelaporan resmi ke Pangkalan Data Pendidikan Tinggi (PDDIKTI).</li>
                <li>Seluruh data sensitif yang tersimpan dalam basis data dienkripsi dengan standar enkripsi simetris (AES-256-CBC) untuk mencegah kebocoran informasi.</li>
                <li>Data Anda tidak akan diperjualbelikan atau dibagikan ke pihak ketiga di luar ekosistem perbankan mitra (H2H Virtual Account) dan lembaga akreditasi pemerintah.</li>
                <li>Persetujuan ini dicatat dengan stempel waktu terverifikasi beserta alamat IP perangkat Anda saat ini.</li>
            </ol>
        </div>

        @if($errors->any())
        <div class="p-3.5 bg-rose-500/20 border border-rose-500/30 rounded-xl text-rose-300 text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form Action -->
        <form action="{{ route('pdp.consent.store') }}" method="POST" class="space-y-4">
            @csrf

            <label class="flex items-start gap-3 p-3 bg-slate-900/60 rounded-xl border border-slate-700 cursor-pointer hover:border-indigo-500 transition">
                <input type="checkbox" name="agree_pdp" value="1" required class="mt-0.5 rounded bg-slate-800 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <span class="text-xs text-slate-300 leading-snug">
                    Saya menyatakan telah membaca, memahami, dan secara sukarela <strong>menyetujui pemrosesan data pribadi saya</strong> untuk keperluan layanan akademik dan administrasi kampus.
                </span>
            </label>

            <div class="flex items-center justify-between pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-slate-400 hover:text-slate-200">
                        Keluar / Batalkan Sesi
                    </button>
                </form>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-lg transition">
                    Setujui &amp; Masuk ke Portal &rarr;
                </button>
            </div>
        </form>

    </div>

</body>
</html>
