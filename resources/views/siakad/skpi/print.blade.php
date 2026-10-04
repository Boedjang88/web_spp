<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Pendamping Ijazah (SKPI) - {{ $skpi['mahasiswa']['nama'] }}</title>
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

    <!-- Action Bar -->
    <div class="no-print max-w-4xl w-full mb-5 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Kembali ke Dashboard
        </a>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
            <span>🖨️</span> Cetak Dokumen SKPI (PDF)
        </button>
    </div>

    <!-- Official SKPI Bilingual Document -->
    <div class="print-card max-w-4xl w-full bg-white rounded-3xl border-2 border-slate-800 shadow-lg p-8 sm:p-12 space-y-8 relative">
        
        <!-- Header & Kop Institusi -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-black text-2xl flex items-center justify-center flex-shrink-0">
                    🏛️
                </div>
                <div>
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</h2>
                    <h1 class="text-lg font-black text-slate-900">UNIVERSITAS TEKNOLOGI NUSANTARA</h1>
                    <p class="text-xs font-semibold text-slate-600 italic">Nusantara University of Technology</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold font-mono text-slate-900 block">{{ $skpi['nomor_skpi'] }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Dokumen Resmi Lampiran Ijazah</span>
            </div>
        </div>

        <!-- Judul Dokumen Bilingual -->
        <div class="text-center space-y-1">
            <h2 class="text-base font-black uppercase tracking-wider text-slate-900">SURAT KETERANGAN PENDAMPING IJAZAH (SKPI)</h2>
            <h3 class="text-xs font-bold text-slate-500 italic tracking-wide">DIPLOMA SUPPLEMENT</h3>
        </div>

        <!-- 1. Informasi Pemegang SKPI -->
        <div class="space-y-3">
            <div class="border-b border-slate-200 pb-1">
                <h4 class="text-xs font-bold text-slate-900 uppercase">1. Informasi Tentang Identitas Pemegang SKPI <span class="text-slate-400 font-normal italic">/ Information Identifying The Holder of The Supplement</span></h4>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Lengkap / Full Name</span>
                    <span class="font-bold text-slate-900">{{ $skpi['mahasiswa']['nama'] }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nomor Induk Mahasiswa (NIM) / Student ID</span>
                    <span class="font-bold font-mono text-slate-900">{{ $skpi['mahasiswa']['nim'] }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Program Studi / Study Program</span>
                    <span class="font-bold text-slate-900">{{ $skpi['mahasiswa']['program_studi_id'] }}</span>
                    <span class="text-slate-500 italic block text-[11px]">{{ $skpi['mahasiswa']['program_studi_en'] }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Gelar Akademik / Awarded Degree</span>
                    <span class="font-bold text-slate-900">{{ $skpi['mahasiswa']['gelar_id'] }}</span>
                    <span class="text-slate-500 italic block text-[11px]">{{ $skpi['mahasiswa']['gelar_en'] }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Capaian SACS & Aktivitas Mahasiswa -->
        <div class="space-y-3">
            <div class="border-b border-slate-200 pb-1 flex justify-between items-center">
                <h4 class="text-xs font-bold text-slate-900 uppercase">2. Rekapitulasi Kredit Aktivitas Mahasiswa (SACS) <span class="text-slate-400 font-normal italic">/ Student Activity Credits</span></h4>
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">
                    Total Poin: {{ $skpi['sacs_summary']['total_poin'] }} Poin SACS
                </span>
            </div>

            <div class="space-y-3 text-xs">
                @foreach($skpi['sacs_summary']['kategori_breakdown'] as $kategori => $group)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                    <div class="flex justify-between items-center font-bold text-slate-800 text-[11px] mb-2 border-b border-slate-200 pb-1">
                        <span>🏆 {{ $kategori }}</span>
                        <span class="text-indigo-600">{{ $group['total_poin'] }} Poin</span>
                    </div>
                    <ul class="space-y-1 text-slate-600">
                        @foreach($group['items'] as $item)
                        <li class="flex justify-between items-center text-[11px]">
                            <span>&bull; {{ $item->nama_kegiatan_id }} <span class="text-slate-400 italic">({{ $item->nama_kegiatan_en }})</span></span>
                            <span class="font-mono text-slate-500">{{ $item->tahun_kegiatan }} &bull; +{{ $item->poin_sacs }} Poin</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Pengesahan & Digital Verification -->
        <div class="grid grid-cols-2 pt-6 border-t-2 border-slate-800 text-xs">
            <div class="space-y-2">
                <span class="text-slate-400 text-[11px] block">Verifikasi Keaslian Dokumen:</span>
                <div class="inline-block p-2 bg-slate-100 border border-slate-300 rounded-xl font-mono text-[9px] text-slate-700">
                    [QR-CODE: {{ $skpi['qr_verification'] }}]
                </div>
            </div>
            <div class="text-right space-y-14">
                <div>
                    <span class="text-[11px] text-slate-500 block">Bandung, {{ date('d F Y') }}</span>
                    <span class="font-bold text-slate-900 block">Dekan Fakultas Teknologi Informasi,</span>
                </div>
                <div>
                    <span class="font-bold text-slate-900 underline block">Prof. Dr. Ir. H. Bambang Hartono, M.T.</span>
                    <span class="font-mono text-[10px] text-slate-500 block">NIP. 196805121993031002</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
