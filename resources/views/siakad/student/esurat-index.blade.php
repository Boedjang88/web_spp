@extends('layouts.app')

@section('title', 'Layanan E-Surat Akademik Mandiri')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">LAYANAN MANDIRI MAHASISWA</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Permohonan E-Surat Akademik Online</h1>
            <p class="text-xs text-blue-200/80 mt-1">Cetak Surat Keterangan Mahasiswa Aktif (SKMA), Transkrip Sementara, &amp; Surat Izin Penelitian mandiri dengan QR Verification Token.</p>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Request Form (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Form Ajukan E-Surat Baru</h2>

            <form method="POST" action="{{ route('siakad.esurat.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jenis Surat Akademik</label>
                    <select name="jenis_surat" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                        <option value="Surat Keterangan Mahasiswa Aktif">Surat Keterangan Mahasiswa Aktif (SKMA)</option>
                        <option value="Surat Izin Penelitian &amp; Skripsi">Surat Izin Penelitian &amp; Skripsi</option>
                        <option value="Surat Pengantar Magang &amp; MBKM">Surat Pengantar Magang &amp; MBKM</option>
                        <option value="Transkrip Nilai Sementara">Transkrip Nilai Sementara</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Perihal Surat</label>
                    <input type="text" name="perihal" value="Surat Keterangan Mahasiswa Aktif" required placeholder="Contoh: Surat Aktif Kuliah"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Keperluan Penggunaan</label>
                    <textarea name="keperluan" rows="3" required placeholder="Contoh: Persyaratan Beasiswa Kemendikbud, BPJS Kesehatan, atau Tunjangan Gaji Ortu..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Terbitkan E-Surat Akademik Baru</span>
                </button>
            </form>
        </div>

        <!-- Right: Surat List (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Daftar E-Surat Akademik Terbit</h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Nomor &amp; Jenis Surat</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Keperluan</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Unduh PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($suratList as $idx => $surat)
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-mono font-bold text-blue-600 dark:text-blue-400 block text-xs">{{ $surat->nomor_surat }}</span>
                                    <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $surat->jenis_surat }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-700 dark:text-slate-300 border-r border-slate-100 dark:border-slate-800">{{ $surat->keperluan }}</td>
                                <td class="py-3 px-3 text-center border-r border-slate-100 dark:border-slate-800">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 border border-emerald-200 dark:border-emerald-800">
                                        {{ $surat->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="#" onclick="alert('Digital Verification Token: {{ substr($surat->qr_verification_token, 0, 24) }}...')" class="px-2.5 py-1 bg-blue-600 text-white rounded text-[11px] font-bold transition">
                                        Unduh PDF &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-mono">Belum ada permohonan E-Surat Akademik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
