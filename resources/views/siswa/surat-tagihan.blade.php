<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tagihan SPP - {{ $siswa->nama }} ({{ $siswa->nisn }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center min-h-screen">

    <!-- Action Bar (Hidden when printing) -->
    <div class="no-print w-full max-w-3xl mb-4 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <a href="{{ route('web.siswa.show', $siswa->id) }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 gap-1">
            &larr; Kembali ke Detail Siswa
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Download PDF
            </button>
        </div>
    </div>

    <!-- Official Document Page (A4 Form Factor) -->
    <div class="print-container w-full max-w-3xl bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-lg text-sm leading-relaxed">
        
        <!-- Official School Header (KOP SURAT) -->
        <div class="border-b-4 border-double border-slate-800 pb-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="w-16 h-16 bg-blue-900 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-md flex-shrink-0">
                    🎓
                </div>
                <div class="text-center flex-1">
                    <h2 class="text-xs uppercase tracking-widest font-bold text-slate-500">Pemerintah Provinsi - Dinas Pendidikan</h2>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">SMK MERDEKA BELAJAR</h1>
                    <p class="text-[11px] text-slate-600 mt-0.5 font-medium">Jl. Pendidikan No. 45, Kompleks Akademika, Telp. (021) 789-0123 | Email: tu@smkmerdeka.sch.id</p>
                </div>
                <div class="w-16 flex-shrink-0 text-right">
                    <span class="text-[9px] font-mono uppercase bg-slate-100 text-slate-600 px-2 py-1 rounded border border-slate-200">FORM-SPP/{{ date('Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Document Metadata -->
        <div class="flex justify-between items-start text-xs mb-6">
            <div>
                <table>
                    <tr>
                        <td class="pr-3 text-slate-500 font-medium">Nomor Surat</td>
                        <td class="pr-2">:</td>
                        <td class="font-mono font-bold text-slate-800">421.5/SPP-{{ str_pad($siswa->id, 4, '0', STR_PAD_LEFT) }}/{{ date('Y') }}</td>
                    </tr>
                    <tr>
                        <td class="pr-3 text-slate-500 font-medium">Lampiran</td>
                        <td class="pr-2">:</td>
                        <td class="text-slate-800">1 (Satu) Berkas Rekapitulasi</td>
                    </tr>
                    <tr>
                        <td class="pr-3 text-slate-500 font-medium">Perihal</td>
                        <td class="pr-2">:</td>
                        <td class="font-semibold text-slate-900">Pemberitahuan & Tagihan Iuran SPP</td>
                    </tr>
                </table>
            </div>
            <div class="text-right text-xs">
                <p class="text-slate-700">Jakarta, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-slate-500 mt-1">Kepada Yth. Orang Tua / Wali dari:</p>
                <p class="font-bold text-slate-900 text-sm">{{ $siswa->nama }}</p>
            </div>
        </div>

        <!-- Content Body -->
        <div class="space-y-4 text-xs text-slate-700">
            <p>Dengan hormat,</p>
            <p class="text-justify leading-relaxed">
                Sehubungan dengan kewajiban administrasi pendidikan dan pemeliharaan fasilitas belajar mengajar di lingkungan SMK Merdeka Belajar, bersama surat ini kami sampaikan rincian status pembayaran Iuran Pembinaan Pendidikan (SPP) atas nama peserta didik berikut:
            </p>

            <!-- Student Data Table -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-3 text-xs">
                <div class="grid grid-cols-2 gap-y-2">
                    <div><span class="text-slate-500">Nama Siswa:</span> <span class="font-bold text-slate-900">{{ $siswa->nama }}</span></div>
                    <div><span class="text-slate-500">NISN / NIS:</span> <span class="font-mono font-semibold text-slate-900">{{ $siswa->nisn }} / {{ $siswa->nis }}</span></div>
                    <div><span class="text-slate-500">Kelas / Jurusan:</span> <span class="font-semibold text-slate-900">{{ $siswa->kelas?->nama_kelas }} ({{ $siswa->kelas?->kompetensi_keahlian }})</span></div>
                    <div><span class="text-slate-500">Tarif SPP:</span> <span class="font-semibold text-slate-900">Rp {{ number_format($siswa->spp?->nominal ?? 0, 0, ',', '.') }} / bulan</span></div>
                </div>
            </div>

            <!-- Breakdown Table -->
            <div>
                <h3 class="font-bold text-slate-900 mb-2">Rekapitulasi Tagihan dan Pembayaran Tahun Ajaran {{ $siswa->spp?->tahun }}:</h3>
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">No</th>
                                <th class="py-2.5 px-3">Bulan SPP</th>
                                <th class="py-2.5 px-3">Nominal Tarif</th>
                                <th class="py-2.5 px-3">Tgl Pembayaran</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $daftarBulan = [
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                                ];
                                $paidMap = $siswa->pembayarans->keyBy('bulan_dibayar');
                                $tunggakan = $siswa->info_tunggakan;
                            @endphp
                            @foreach($daftarBulan as $idx => $bln)
                                @php $pemb = $paidMap->get($bln); @endphp
                                <tr class="{{ $pemb ? 'bg-white' : 'bg-rose-50/40' }}">
                                    <td class="py-2 px-3 text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2 px-3 font-semibold text-slate-800">{{ $bln }}</td>
                                    <td class="py-2 px-3 text-slate-700">Rp {{ number_format($siswa->spp?->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-600">{{ $pemb ? $pemb->tgl_bayar : '-' }}</td>
                                    <td class="py-2 px-3">
                                        @if($pemb)
                                            <span class="inline-flex items-center gap-1 font-bold text-emerald-700 text-[11px]">
                                                ✓ LUNAS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-rose-700 text-[11px]">
                                                ⚠ BELUM DIBAYAR
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-xs">
                            <tr>
                                <td colspan="2" class="py-2.5 px-3 text-right">Total Tunggakan:</td>
                                <td colspan="3" class="py-2.5 px-3 {{ $tunggakan['total_rupiah'] > 0 ? 'text-rose-700' : 'text-emerald-700' }} text-sm font-black">
                                    Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500">({{ $tunggakan['total_bulan'] }} bulan)</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="border border-blue-100 bg-blue-50/60 rounded-xl p-3.5 mt-4 text-[11px] text-blue-900">
                <span class="font-bold block mb-1">Metode Pelunasan Tagihan:</span>
                <ol class="list-decimal list-inside space-y-0.5 text-blue-800">
                    <li>Loket Kasir Tata Usaha Sekolah pada jam operasional (Senin - Jumat 07.30 - 15.00 WIB).</li>
                    <li>Transfer Virtual Account / Bank BNI: <strong>988-1234-{{ $siswa->nisn }}</strong> a.n. SMK Merdeka Belajar.</li>
                </ol>
            </div>

            <p class="mt-4 leading-relaxed text-slate-600">
                Demikian surat pemberitahuan ini kami sampaikan. Atas perhatian dan kerja sama yang baik dari Bapak/Ibu Wali Murid, kami ucapkan terima kasih.
            </p>
        </div>

        <!-- Signature Section -->
        <div class="mt-8 pt-4 flex justify-between items-end text-xs text-slate-800">
            <div class="text-center w-48">
                <p class="text-slate-500 mb-16">Mengetahui,<br>Orang Tua / Wali Murid</p>
                <div class="border-b border-slate-400 w-36 mx-auto"></div>
                <p class="text-[10px] text-slate-400 mt-1">(Nama & Tanda Tangan)</p>
            </div>

            <div class="text-center w-56 relative">
                <!-- Stempel Cap Digital Effect -->
                <div class="absolute right-4 top-4 w-24 h-24 border-2 border-blue-600/40 rounded-full flex items-center justify-center -rotate-12 pointer-events-none opacity-60">
                    <span class="text-[8px] uppercase tracking-tighter font-black text-blue-700 text-center">TATA USAHA<br>SMK MERDEKA<br>BELAJAR</span>
                </div>

                <p class="text-slate-500 mb-16">Jakarta, {{ now()->translatedFormat('d F Y') }}<br>Kepala Tata Usaha,</p>
                <div class="border-b border-slate-800 w-44 mx-auto"></div>
                <p class="font-bold text-slate-900 mt-1">Drs. H. Hendra Wijaya, M.Pd</p>
                <p class="text-[10px] text-slate-500">NIP. 19780514 200312 1 002</p>
            </div>
        </div>

    </div>

</body>
</html>
