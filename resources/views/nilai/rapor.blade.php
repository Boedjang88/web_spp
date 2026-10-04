<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Rapor Hasil Belajar - {{ $siswa->nama }} ({{ $siswa->nisn }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center min-h-screen">

    <!-- Action Bar (Hidden when printing) -->
    <div class="no-print w-full max-w-4xl mb-4 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <a href="{{ route('web.nilai.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 gap-1">
            &larr; Kembali ke Data Nilai
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Simpan Rapor PDF
        </button>
    </div>

    <!-- Official Report Document Container (A4) -->
    <div class="print-container w-full max-w-4xl bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-lg text-xs leading-relaxed">
        
        <!-- Official School Header -->
        <div class="border-b-4 border-double border-slate-800 pb-4 mb-6">
            <div class="flex items-center justify-between gap-4">
                <div class="w-16 h-16 bg-blue-900 rounded-2xl flex items-center justify-center text-white text-3xl font-black shadow-md flex-shrink-0">
                    🎓
                </div>
                <div class="text-center flex-1">
                    <h2 class="text-xs uppercase tracking-widest font-bold text-slate-500">Pemerintah Daerah - Dinas Pendidikan</h2>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">SMK MERDEKA BELAJAR</h1>
                    <p class="text-[11px] text-slate-600 mt-0.5 font-medium">Jl. Pendidikan No. 45, Kompleks Akademika | Akreditasi: A (Unggul)</p>
                </div>
                <div class="w-16 flex-shrink-0 text-right">
                    <span class="text-[9px] font-mono uppercase bg-slate-100 text-slate-600 px-2 py-1 rounded border border-slate-200">RAPOR-SIAKAD</span>
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-4">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">LAPORAN HASIL CAPAIAN KOMPETENSI PESERTA DIDIK</h2>
            <p class="text-slate-500 text-[11px] font-semibold">Tahun Ajaran {{ $tahunAjaran }} &bull; Semester {{ $semester }}</p>
        </div>

        <!-- Student Meta Details -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-4">
            <div class="grid grid-cols-2 gap-y-2">
                <div><span class="text-slate-500">Nama Peserta Didik:</span> <strong class="text-slate-900 font-bold">{{ $siswa->nama }}</strong></div>
                <div><span class="text-slate-500">NISN / NIS:</span> <span class="font-mono font-bold text-slate-900">{{ $siswa->nisn }} / {{ $siswa->nis }}</span></div>
                <div><span class="text-slate-500">Kelas / Kompetensi:</span> <strong class="text-slate-900 font-bold">{{ $siswa->kelas?->nama_kelas }} ({{ $siswa->kelas?->kompetensi_keahlian }})</strong></div>
                <div><span class="text-slate-500">Semester / Tahun:</span> <span class="font-semibold text-slate-900">{{ $semester }} / {{ $tahunAjaran }}</span></div>
            </div>
        </div>

        <!-- Academic Grades Table -->
        <div class="my-5">
            <h3 class="font-bold text-slate-900 mb-2">A. Nilai Capaian Akademik:</h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Mata Pelajaran</th>
                            <th class="py-2.5 px-3 text-center">KKM</th>
                            <th class="py-2.5 px-3 text-center">Tugas</th>
                            <th class="py-2.5 px-3 text-center">UTS</th>
                            <th class="py-2.5 px-3 text-center">UAS</th>
                            <th class="py-2.5 px-3 text-center font-bold">Nilai Akhir</th>
                            <th class="py-2.5 px-3 text-center">Predikat</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($nilais as $idx => $n)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-2 px-3">
                                    <div class="font-bold text-slate-900">{{ $n->mapel?->nama_mapel }}</div>
                                    <div class="text-[10px] text-slate-500">Pengampu: {{ $n->guru?->nama_guru ?? '-' }}</div>
                                </td>
                                <td class="py-2 px-3 text-center font-mono">{{ $n->mapel?->kkm }}</td>
                                <td class="py-2 px-3 text-center font-mono">{{ (float) $n->nilai_tugas }}</td>
                                <td class="py-2 px-3 text-center font-mono">{{ (float) $n->nilai_uts }}</td>
                                <td class="py-2 px-3 text-center font-mono">{{ (float) $n->nilai_uas }}</td>
                                <td class="py-2 px-3 text-center font-mono font-bold text-slate-900">{{ (float) $n->nilai_akhir }}</td>
                                <td class="py-2 px-3 text-center font-bold">{{ $n->predikat }}</td>
                                <td class="py-2 px-3">
                                    @if($n->nilai_akhir >= ($n->mapel?->kkm ?? 75))
                                        <span class="text-emerald-700 font-bold text-[11px]">✓ Tuntas</span>
                                    @else
                                        <span class="text-rose-700 font-bold text-[11px]">⚠ Remedi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-slate-400">Belum ada nilai akademik yang diinput untuk semester ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($nilais->count() > 0)
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="6" class="py-2 px-3 text-right">Rata-Rata Nilai:</td>
                                <td class="py-2 px-3 text-center font-mono font-black text-blue-800 text-sm">
                                    {{ round($nilais->avg('nilai_akhir'), 2) }}
                                </td>
                                <td colspan="2" class="py-2 px-3 text-slate-600">
                                    Status: <span class="text-emerald-700 font-bold">Lulus Semester</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="my-5">
            <h3 class="font-bold text-slate-900 mb-2">B. Rekapitulasi Kehadiran &amp; Presensi:</h3>
            <div class="grid grid-cols-4 gap-3">
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                    <span class="text-[11px] font-semibold text-emerald-800 block">Hadir</span>
                    <span class="text-xl font-black text-emerald-900">{{ $kehadiran['hadir'] }} Hari</span>
                </div>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-center">
                    <span class="text-[11px] font-semibold text-blue-800 block">Izin</span>
                    <span class="text-xl font-black text-blue-900">{{ $kehadiran['izin'] }} Hari</span>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center">
                    <span class="text-[11px] font-semibold text-amber-800 block">Sakit</span>
                    <span class="text-xl font-black text-amber-900">{{ $kehadiran['sakit'] }} Hari</span>
                </div>
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-center">
                    <span class="text-[11px] font-semibold text-rose-800 block">Tanpa Keterangan</span>
                    <span class="text-xl font-black text-rose-900">{{ $kehadiran['alpa'] }} Hari</span>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="mt-10 pt-4 grid grid-cols-3 gap-4 text-center text-slate-800">
            <div>
                <p class="text-slate-500 mb-16">Mengetahui,<br>Orang Tua / Wali</p>
                <div class="border-b border-slate-400 w-32 mx-auto"></div>
                <p class="text-[10px] text-slate-400 mt-1">(....................................)</p>
            </div>

            <div>
                <p class="text-slate-500 mb-16">Jakarta, {{ now()->translatedFormat('d F Y') }}<br>Wali Kelas,</p>
                <div class="border-b border-slate-800 w-36 mx-auto"></div>
                <p class="font-bold text-slate-900 mt-1">{{ $siswa->kelas?->nama_kelas }} Advisor</p>
                <p class="text-[10px] text-slate-500">NIP. 19820311 200801 1 004</p>
            </div>

            <div class="relative">
                <div class="absolute right-4 top-2 w-20 h-20 border-2 border-blue-600/40 rounded-full flex items-center justify-center -rotate-12 pointer-events-none opacity-60">
                    <span class="text-[7px] uppercase tracking-tighter font-black text-blue-700 text-center">KEPALA SEKOLAH<br>SMK MERDEKA<br>BELAJAR</span>
                </div>

                <p class="text-slate-500 mb-16">Mengetahui,<br>Kepala Sekolah,</p>
                <div class="border-b border-slate-800 w-36 mx-auto"></div>
                <p class="font-bold text-slate-900 mt-1">Dr. Ir. H. Suryadi, M.Kom</p>
                <p class="text-[10px] text-slate-500">NIP. 19710920 199703 1 003</p>
            </div>
        </div>

    </div>

</body>
</html>
