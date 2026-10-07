@extends('layouts.app')

@section('title', 'Berita Acara Perkuliahan (BAP) Dosen')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">BERITA ACARA PERKULIAHAN (BAP)</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Digital BAP &amp; Signature Verification</h1>
            <p class="text-xs text-blue-200/80 mt-1">Pengisian Jurnal Perkuliahan Pertemuan 1 - 16 lengkap dengan verifikasi Digital Signature Hash.</p>
        </div>
    </div>

    <!-- Class Selector Bar -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-4 shadow-sm">
        <form method="GET" action="{{ route('siakad.dosen.bap.index') }}" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
            <div class="w-full sm:w-80">
                <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Pilih Kelas Perkuliahan</label>
                <select name="id_kelas_kuliah" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500">
                    @foreach($kelasKuliahs as $kk)
                        <option value="{{ $kk->id }}" {{ $selectedKelasId == $kk->id ? 'selected' : '' }}>
                            {{ $kk->mataKuliah?->kode_mk }} - {{ $kk->mataKuliah?->nama_mk }} ({{ $kk->nama_kelas }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- BAP Form & List -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Input BAP Form (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Input Jurnal BAP Pertemuan</h2>

            <form method="POST" action="{{ route('siakad.dosen.bap.store') }}" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="id_kelas_kuliah" value="{{ $selectedKelasId }}">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Pertemuan Ke-</label>
                        <select name="pertemuan_ke" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl">
                            @for($p = 1; $p <= 16; $p++)
                                <option value="{{ $p }}">Pertemuan {{ $p }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jumlah Hadir</label>
                        <input type="number" name="total_mahasiswa_hadir" value="38" required min="0"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal_pelaksanaan" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jam Mulai</label>
                        <input type="time" name="jam_mulai_real" value="08:00" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Jam Selesai</label>
                        <input type="time" name="jam_selesai_real" value="10:30" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Materi Pembahasan Perkuliahan</label>
                    <textarea name="materi_pembahasan" rows="2" required placeholder="Pokok materi & modul yang disampaikan..."
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Catatan Evaluasi Dosen</label>
                    <textarea name="catatan_dosen" rows="2" placeholder="Catatan keaktifan atau evaluasi mahasiswa..."
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Simpan BAP &amp; Tanda Tangan Digital</span>
                </button>
            </form>
        </div>

        <!-- Right: List BAP Records (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Rekap BAP &amp; Digital Signature Hash</h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3 w-10 text-center border-r border-slate-200 dark:border-slate-700">P-</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700">Materi Pembahasan</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700 text-center">Tgl / Jam</th>
                            <th class="py-3 px-3 border-r border-slate-200 dark:border-slate-700 text-center">Hadir</th>
                            <th class="py-3 px-3 text-center">Digital Signature / Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($bapList as $bap)
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3 text-center font-bold text-blue-600 dark:text-blue-400 font-mono border-r border-slate-100 dark:border-slate-800">P{{ $bap->pertemuan_ke }}</td>
                                <td class="py-3 px-3 border-r border-slate-100 dark:border-slate-800">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $bap->materi_pembahasan }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">Hash: {{ substr($bap->digital_signature_hash, 0, 16) }}...</span>
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-[11px] border-r border-slate-100 dark:border-slate-800">
                                    <span>{{ $bap->tanggal_pelaksanaan }}</span>
                                    <span class="block text-slate-400 text-[10px]">{{ substr($bap->jam_mulai_real, 0, 5) }} - {{ substr($bap->jam_selesai_real, 0, 5) }}</span>
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-emerald-600 font-mono border-r border-slate-100 dark:border-slate-800">
                                    {{ $bap->total_mahasiswa_hadir }} Mhs
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="{{ route('siakad.bap.print', $bap->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-700 dark:text-slate-200 rounded text-[11px] font-bold font-mono transition">
                                        Cetak PDF &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-mono">Belum ada jurnal BAP untuk kelas perkuliahan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
