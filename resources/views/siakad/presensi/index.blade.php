@extends('layouts.app')

@section('title', 'Presensi Perkuliahan Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Executive Header Banner -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-blue-900/50 shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-blue-200 border border-white/10 text-[10px] font-mono font-bold uppercase mb-2">
                <span>GEO-FENCED GPS &bull; RADIUS 20M</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Presensi Perkuliahan Mahasiswa</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">Catat kehadiran kelas Anda secara real-time dengan verifikasi koordinat lokasi GPS dan kode token presensi dari dosen.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right">
                <div class="text-[10px] text-slate-300 font-mono uppercase tracking-wider font-bold">Tingkat Kehadiran</div>
                <div class="text-xl font-black font-mono text-emerald-400">{{ $kehadiranPersen }}%</div>
            </div>
        </div>
    </div>

    <!-- Visual Attendance Progress Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-soft space-y-2">
        <div class="flex items-center justify-between text-xs font-bold">
            <span class="text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">Progres Kehadiran Semester Ini</span>
            <span class="text-blue-600 dark:text-blue-400 font-mono font-black">{{ $kehadiranPersen }}%</span>
        </div>
        <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5">
            <div class="h-full bg-gradient-to-r from-blue-600 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $kehadiranPersen)) }}%"></div>
        </div>
    </div>

    <!-- Attendance Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/60 shadow-soft">
            <div class="text-[10px] font-mono text-emerald-700 dark:text-emerald-300 font-bold uppercase tracking-wider">
                ✓ Hadir
            </div>
            <div class="text-2xl font-black font-mono text-emerald-700 dark:text-emerald-300 mt-1">{{ $totalHadir }} <span class="text-xs text-emerald-600/70 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/60 shadow-soft">
            <div class="text-[10px] font-mono text-amber-700 dark:text-amber-300 font-bold uppercase tracking-wider">
                ⏱ Izin
            </div>
            <div class="text-2xl font-black font-mono text-amber-700 dark:text-amber-300 mt-1">{{ $totalIzin }} <span class="text-xs text-amber-600/70 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/60 shadow-soft">
            <div class="text-[10px] font-mono text-blue-700 dark:text-blue-300 font-bold uppercase tracking-wider">
                🏥 Sakit
            </div>
            <div class="text-2xl font-black font-mono text-blue-700 dark:text-blue-300 mt-1">{{ $totalSakit }} <span class="text-xs text-blue-600/70 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-900/60 shadow-soft">
            <div class="text-[10px] font-mono text-rose-700 dark:text-rose-300 font-bold uppercase tracking-wider">
                ⚠ Alpa
            </div>
            <div class="text-2xl font-black font-mono text-rose-700 dark:text-rose-300 mt-1">{{ $totalAlpa }} <span class="text-xs text-rose-600/70 font-normal">Sesi</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sesi Kuliah Hari Ini & Form Check-In -->
        <div class="lg:col-span-1 space-y-4">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-soft space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 pb-3 border-b border-slate-100 dark:border-slate-800">
                    Check-In Presensi Kuliah
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Masukkan kode token QR dari dosen saat perkuliahan berlangsung dan pastikan izin GPS browser aktif.</p>

                <form action="{{ route('siakad.presensi.checkIn') }}" method="POST" id="attendanceForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Sesi Perkuliahan Aktif</label>
                        <select name="id_bap" id="id_bap" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 font-medium">
                            <option value="">-- Pilih Sesi Perkuliahan --</option>
                            @foreach($todayBaps as $bap)
                                <option value="{{ $bap->id }}">
                                    {{ $bap->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }} - {{ $bap->materi_pembahasan ?? 'Materi Perkuliahan' }} (P{{ $bap->pertemuan_ke }} - {{ $bap->ruangan?->nama_ruangan ?? 'Ruang' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Token Presensi Dosen</label>
                        <input type="text" name="qr_token" id="qr_token" placeholder="Contoh: ATT-QR-XXXXXXXX" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 font-mono uppercase tracking-wider font-semibold">
                        <span class="text-[10px] text-slate-400 mt-1 block font-mono">Token QR berputar otomatis setiap 10 detik.</span>
                    </div>

                    <!-- Hidden Coordinate Fields -->
                    <input type="hidden" name="latitude" id="latInput" value="-6.917464">
                    <input type="hidden" name="longitude" id="lngInput" value="107.619123">

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 text-[11px] text-slate-600 dark:text-slate-400 flex items-center justify-between font-mono">
                        <span id="gpsStatus">GPS: Siap</span>
                        <button type="button" onclick="detectGPS()" class="text-blue-600 dark:text-blue-400 hover:underline font-bold text-[11px]">
                            Deteksi GPS
                        </button>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                        <span>Kirim Presensi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- History Table (2 Cols) -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-soft space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 pb-3 border-b border-slate-100 dark:border-slate-800">
                Riwayat Presensi Kehadiran Perkuliahan
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left font-mono">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Tanggal &amp; Waktu</th>
                            <th class="py-3 px-4">Mata Kuliah</th>
                            <th class="py-3 px-4">Ruangan</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($attendanceHistory as $p)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                    {{ $p->waktu_hadir ? \Carbon\Carbon::parse($p->waktu_hadir)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-sans font-bold text-slate-900 dark:text-slate-100">
                                    {{ $p->bap?->kelasKuliah?->mataKuliah?->nama_mk ?? $p->kelasKuliah?->mataKuliah?->nama_mk ?? '-' }}
                                </td>
                                <td class="py-3 px-4 font-sans text-slate-500 dark:text-slate-400">
                                    {{ $p->bap?->ruangan?->nama_ruangan ?? 'Kelas' }}
                                </td>
                                <td class="py-3 px-4 font-sans">
                                    @if($p->status === 'Hadir')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-bold">✓ Hadir</span>
                                    @elseif($p->status === 'Izin')
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-[10px] font-bold">⏱ Izin</span>
                                    @elseif($p->status === 'Sakit')
                                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-[10px] font-bold">🏥 Sakit</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-[10px] font-bold">⚠ Alpa</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-sans">
                                    Belum ada data presensi perkuliahan yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
