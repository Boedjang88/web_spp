@extends('layouts.app')

@section('title', 'Early Warning System (EWS DO) - BAAK')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-red-950 via-slate-900 to-amber-950 p-6 rounded-2xl border border-red-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-500/20 text-red-200 border border-red-400/30">RISK CONTROL BAAK</span>
                <span class="text-xs text-red-200/80">Deteksi Dini Drop Out &amp; Kepatuhan Masa Studi</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Early Warning System (EWS) Akademik</h1>
            <p class="text-xs text-red-200/80 mt-1">Sistem otomatis pemantauan risiko IPK rendah, absensi minim, dan kelalaian pengisian KRS mahasiswa.</p>
        </div>
        <form action="{{ route('siakad.baak.ews.scan') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl text-xs shadow-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Jalankan Pemindaian EWS Massal</span>
            </button>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase font-mono tracking-wider block">Bahaya Kritis (Critical)</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalCritical }} Mahasiswa</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center font-bold text-lg">!</div>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase font-mono tracking-wider block">Tinggi (High Risk)</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalHigh }} Mahasiswa</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-lg">&Delta;</div>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase font-mono tracking-wider block">Sedang (Medium Warning)</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalMedium }} Mahasiswa</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold text-lg">i</div>
        </div>
    </div>

    <!-- Warnings Table -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Log Peringatan Dini Akademik Mahasiswa</h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3.5 px-4 w-12 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">Level Tingkat Risiko</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">NIM / Nama Mahasiswa</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">Kelas / Prodi</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">Jenis Pelanggaran Risiko</th>
                        <th class="py-3.5 px-4">Deskripsi / Detail Penyebab</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($warnings as $idx => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase {{ $item->severity === 'CRITICAL' ? 'bg-red-50 dark:bg-red-950/60 text-red-600 border border-red-200 dark:border-red-800' : ($item->severity === 'HIGH' ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 border border-amber-200 dark:border-amber-800' : 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 border border-blue-200 dark:border-blue-800') }}">
                                    {{ $item->severity }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 block text-xs">{{ $item->siswa?->nisn ?? $item->siswa?->nis }}</span>
                                <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $item->siswa?->nama }}</span>
                            </td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $item->siswa?->kelas?->nama_kelas }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-100 dark:border-slate-800">
                                {{ $item->trigger_type }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                {{ $item->trigger_reason }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-mono">Belum ada peringatan risiko DO terdeteksi. Silakan jalankan pemindaian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $warnings->links() }}
        </div>
    </div>
</div>
@endsection
