@extends('layouts.app')

@section('title', 'Peminjaman Fasilitas Kampus')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">FASILITAS KAMPUS ENTERPRISE</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Peminjaman Ruang Lab &amp; Fasilitas Kampus</h1>
            <p class="text-xs text-blue-200/80 mt-1">Booking fasilitas laboratorium AI, Workstation, Auditorium, &amp; Ruang Seminar secara online.</p>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Form Booking (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Form Pengajuan Booking Fasilitas</h2>

            <form method="POST" action="{{ route('siakad.fasilitas.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Pilih Fasilitas Kampus</label>
                    <select name="id_facility" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                        @foreach($facilities as $f)
                            <option value="{{ $f->id }}">
                                {{ $f->nama_fasilitas }} (Kapasitas: {{ $f->kapasitas }} orang)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Tanggal Peminjaman</label>
                    <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="09:00" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="12:00" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Tujuan &amp; Agenda Penggunaan</label>
                    <textarea name="tujuan_penggunaan" rows="3" required placeholder="Contoh: Praktikum Mandiri Kelompok, Workshop AI &amp; Cloud Architecture..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Kirim Pengajuan Booking Fasilitas</span>
                </button>
            </form>
        </div>

        <!-- Right: Booking Records List (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Status Permohonan Booking Fasilitas</h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Fasilitas Kampus</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Pemohon &amp; Agenda</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700 text-center">Tgl / Jam</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($bookings as $idx => $b)
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $b->facility?->nama_fasilitas }}</span>
                                    <span class="text-[10px] text-blue-500 font-mono block">{{ $b->facility?->kategori }} &bull; Max {{ $b->facility?->kapasitas }} Orang</span>
                                </td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $b->pemohon?->name }}</span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $b->tujuan_penggunaan }}</span>
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-[11px] border-r border-slate-100 dark:border-slate-800">
                                    <span>{{ $b->tanggal_pinjam }}</span>
                                    <span class="block text-slate-400 text-[10px]">{{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }}</span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase {{ $b->status_booking === 'APPROVED' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 border border-emerald-200 dark:border-emerald-800' : ($b->status_booking === 'REJECTED' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 border border-rose-200 dark:border-rose-800' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 border border-amber-200 dark:border-amber-800') }}">
                                        {{ $b->status_booking }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-mono">Belum ada data peminjaman fasilitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>

    </div>

</div>
@endsection
