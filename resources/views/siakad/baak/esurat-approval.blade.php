@extends('layouts.app')

@section('title', 'Persetujuan E-Surat Akademik - BAAK')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">VERIFIKASI BAAK</span>
                <span class="text-xs text-blue-200/80">Persetujuan E-Surat Akademik Mahasiswa</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Persetujuan &amp; Verifikasi E-Surat Akademik</h1>
            <p class="text-xs text-blue-200/80 mt-1">Validasi pengajuan Surat Keterangan Mahasiswa Aktif, Transkrip Sementara, dan Surat Izin Riset.</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('siakad.baak.esurat.index', ['status' => 'MENUNGGU_PERSETUJUAN']) }}" class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between hover:border-amber-500 transition">
            <div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase font-mono tracking-wider block">Menunggu Persetujuan</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalPending }} Permohonan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-lg">&Delta;</div>
        </a>
        <a href="{{ route('siakad.baak.esurat.index', ['status' => 'DISETUJUI']) }}" class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between hover:border-emerald-500 transition">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase font-mono tracking-wider block">Disetujui / Terbit</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalApproved }} Surat</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-lg">&check;</div>
        </a>
        <a href="{{ route('siakad.baak.esurat.index', ['status' => 'DITOLAK']) }}" class="bg-white dark:bg-[#1c2541] p-5 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm flex items-center justify-between hover:border-rose-500 transition">
            <div>
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase font-mono tracking-wider block">Ditolak</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1 block">{{ $totalRejected }} Permohonan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-bold text-lg">&times;</div>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Daftar Pengajuan E-Surat</h2>
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('siakad.baak.esurat.index', ['status' => 'ALL']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ $statusFilter === 'ALL' ? 'bg-blue-600 text-white border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Semua</a>
                <a href="{{ route('siakad.baak.esurat.index', ['status' => 'MENUNGGU_PERSETUJUAN']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ $statusFilter === 'MENUNGGU_PERSETUJUAN' ? 'bg-amber-600 text-white border-amber-600' : 'text-slate-600 hover:bg-slate-50' }}">Menunggu</a>
                <a href="{{ route('siakad.baak.esurat.index', ['status' => 'DISETUJUI']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ $statusFilter === 'DISETUJUI' ? 'bg-emerald-600 text-white border-emerald-600' : 'text-slate-600 hover:bg-slate-50' }}">Disetujui</a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                        <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Pemohon / Mahasiswa</th>
                        <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Nomor &amp; Jenis Surat</th>
                        <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Keperluan</th>
                        <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($suratList as $idx => $surat)
                        <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                            <td class="py-3 px-3 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                            <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-bold text-slate-900 dark:text-white block text-xs">{{ $surat->siswa?->nama ?? 'Mahasiswa #' . $surat->id_siswa }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">NIM: {{ $surat->siswa?->nisn ?? $surat->siswa?->nis ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 block text-xs">{{ $surat->nomor_surat }}</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs">{{ $surat->jenis_surat }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-700 dark:text-slate-300 border-r border-slate-100 dark:border-slate-800">{{ $surat->keperluan }}</td>
                            <td class="py-3 px-3 text-center border-r border-slate-100 dark:border-slate-800">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase {{ $surat->status === 'DISETUJUI' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($surat->status === 'DITOLAK' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-amber-50 text-amber-600 border border-amber-200') }}">
                                    {{ $surat->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($surat->status === 'MENUNGGU_PERSETUJUAN')
                                    <div class="flex items-center justify-center gap-1.5">
                                        <form method="POST" action="{{ route('siakad.baak.esurat.approve', $surat->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-[11px] transition shadow-sm">
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('siakad.baak.esurat.reject', $surat->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-lg text-[11px] transition shadow-sm">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 font-mono">Verifikasi Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-mono">Tidak ada permohonan e-Surat dengan status ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $suratList->links() }}
        </div>
    </div>
</div>
@endsection
