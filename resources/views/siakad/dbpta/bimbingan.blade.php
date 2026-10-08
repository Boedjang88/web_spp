@extends('layouts.app')

@section('title', 'DBPTA - Logbook Bimbingan Skripsi')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-blue-950 p-6 rounded-2xl border border-indigo-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-200 border border-indigo-400/30">MODUL DBPTA</span>
                <span class="text-xs text-indigo-200/80">Logbook Digital Bimbingan Tugas Akhir</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Logbook Bimbingan Skripsi / TA</h1>
            <p class="text-xs text-indigo-200/80 mt-1">Pencatatan konsultasi berkala dengan Dosen Pembimbing Utama &amp; Pendamping.</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Tambah Logbook -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Catat Sesi Bimbingan</h2>

            <form method="POST" action="{{ route('siakad.dbpta.bimbingan.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-indigo-100 mb-1 font-mono">Bab / Topik Pembahasan <span class="text-rose-500">*</span></label>
                    <select name="bab" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                        <option value="BAB I - Pendahuluan">BAB I - Pendahuluan &amp; Latar Belakang</option>
                        <option value="BAB II - Tinjauan Pustaka">BAB II - Tinjauan Pustaka &amp; Landasan Teori</option>
                        <option value="BAB III - Metodologi">BAB III - Metodologi Penelitian</option>
                        <option value="BAB IV - Hasil &amp; Pembahasan">BAB IV - Hasil &amp; Pembahasan System</option>
                        <option value="BAB V - Kesimpulan">BAB V - Kesimpulan &amp; Saran</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-indigo-100 mb-1 font-mono">Materi / Progres Konsultasi <span class="text-rose-500">*</span></label>
                    <textarea name="materi_bimbingan" rows="3" required placeholder="Jelaskan progres revisi atau materi yang didiskusikan..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-indigo-100 mb-1 font-mono">Catatan / Arahan Dosen</label>
                    <textarea name="saran_dosen" rows="2" placeholder="Catatan perbaikan dari Dosen Pembimbing..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Simpan Logbook Bimbingan</span>
                </button>
            </form>
        </div>

        <!-- History Table -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Riwayat Logbook Bimbingan</h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Bab / Tanggal</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Materi &amp; Catatan Dosen</th>
                            <th class="py-3 px-3 text-center">Paraf Dosen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($logbookList as $idx => $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 block text-xs">{{ $log->bab }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->tanggal_bimbingan ? $log->tanggal_bimbingan->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-700 dark:text-slate-300 border-r border-slate-100 dark:border-slate-800 space-y-1">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $log->materi_bimbingan }}</div>
                                    <div class="text-[11px] text-slate-500 italic">Arahan Dosen: {{ $log->catatan_dosen ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold font-mono bg-emerald-50 text-emerald-600 border border-emerald-200">ACC PARAF</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-mono">Belum ada riwayat bimbingan. Silakan tambah logbook.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
