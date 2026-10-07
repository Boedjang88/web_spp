@extends('layouts.app')

@section('title', 'Presensi Kehadiran Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-400/30 font-mono">SIAKAD ENTERPRISE</span>
                <span class="text-xs text-slate-400">Pengelolaan Kehadiran Perkuliahan Dosen</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">Presensi Kehadiran Mahasiswa</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Wewenang Dosen Pengampu &amp; BAAK dalam mencatat/memperbarui kehadiran mahasiswa per kelas kuliah (Hadir, Izin, Sakit, Alpa)</p>
        </div>
    </div>

    <!-- Class, Date, Semester & Jurusan Selector Card -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-5">
        <form method="GET" action="{{ route('web.presensi.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Pilih Kelas Kuliah</label>
                <select name="id_kelas" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kelas Kuliah --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->kompetensi_keahlian }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Tanggal Perkuliahan</label>
                <input type="date" name="tanggal" value="{{ $selectedTanggal }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Filter Jurusan / Prodi</label>
                <select name="prodi_filter" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jurusan</option>
                    <option value="Teknik Informatika" {{ request('prodi_filter') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                    <option value="Sistem Informasi" {{ request('prodi_filter') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                    <option value="Bisnis Digital" {{ request('prodi_filter') == 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 px-5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Buka Lembar Presensi</span>
                </button>
            </div>
        </form>
    </div>

    @if($selectedKelasId)
        <!-- Attendance Sheet Card -->
        <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm overflow-hidden">
            
            <div class="p-5 bg-slate-50/50 dark:bg-[#0b132b]/60 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <span class="font-bold text-slate-900 dark:text-white text-sm">Lembar Kehadiran Mahasiswa ({{ $siswas->count() }} Mahasiswa Terdaftar)</span>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Tanggal Perkuliahan: <strong class="font-mono text-blue-600 dark:text-blue-400">{{ \Carbon\Carbon::parse($selectedTanggal)->translatedFormat('l, d F Y') }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setSemuaStatus('Hadir')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Set Semua Hadir</span>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('web.presensi.batch') }}">
                @csrf
                <input type="hidden" name="id_kelas" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $selectedTanggal }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 uppercase tracking-wider text-[10px] font-bold">
                                <th class="py-3.5 px-4 font-bold border-r border-slate-200 dark:border-slate-700 w-12 text-center">No</th>
                                <th class="py-3.5 px-4 font-bold border-r border-slate-200 dark:border-slate-700">NIM / NISN</th>
                                <th class="py-3.5 px-4 font-bold border-r border-slate-200 dark:border-slate-700">Nama Mahasiswa</th>
                                <th class="py-3.5 px-4 font-bold border-r border-slate-200 dark:border-slate-700 text-center">Status Kehadiran (Wewenang Dosen)</th>
                                <th class="py-3.5 px-4 font-bold">Catatan Opsional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($siswas as $idx => $s)
                                @php
                                    $existingPresensi = $s->presensis->first();
                                    $currentStatus = $existingPresensi ? $existingPresensi->status : 'Hadir';
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                    <td class="py-3.5 px-4 text-slate-400 dark:text-slate-500 font-mono text-center border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 border-r border-slate-100 dark:border-slate-800">{{ $s->nim ?? $s->nisn }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white border-r border-slate-100 dark:border-slate-800">{{ $s->nama }}</td>
                                    <td class="py-3.5 px-4 text-center border-r border-slate-100 dark:border-slate-800">
                                        <input type="hidden" name="presensi[{{ $idx }}][id_siswa]" value="{{ $s->id }}">
                                        <div class="inline-flex items-center gap-4">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Hadir" {{ $currentStatus == 'Hadir' ? 'checked' : '' }} class="status-radio-Hadir text-emerald-600 focus:ring-emerald-500">
                                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Hadir</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Izin" {{ $currentStatus == 'Izin' ? 'checked' : '' }} class="status-radio-Izin text-blue-600 focus:ring-blue-500">
                                                <span class="font-bold text-blue-600 dark:text-blue-400">Izin</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Sakit" {{ $currentStatus == 'Sakit' ? 'checked' : '' }} class="status-radio-Sakit text-amber-600 focus:ring-amber-500">
                                                <span class="font-bold text-amber-600 dark:text-amber-400">Sakit</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Alpa" {{ $currentStatus == 'Alpa' ? 'checked' : '' }} class="status-radio-Alpa text-rose-600 focus:ring-rose-500">
                                                <span class="font-bold text-rose-600 dark:text-rose-400">Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <input type="text" name="presensi[{{ $idx }}][keterangan]" value="{{ $existingPresensi?->keterangan }}" placeholder="Catatan Dosen..."
                                            class="w-full px-3 py-1.5 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 font-mono">Belum ada mahasiswa terdaftar di kelas perkuliahan yang dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswas->count() > 0)
                    <div class="p-5 bg-slate-50/50 dark:bg-[#0b132b]/60 border-t border-slate-200/80 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Simpan Kehadiran Mahasiswa</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="p-12 text-center text-slate-400 bg-white dark:bg-[#1c2541] rounded-2xl border border-dashed border-slate-200/80 dark:border-blue-900/50">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 mx-auto mb-3 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Silakan pilih kelas kuliah dan tanggal perkuliahan di atas</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar mahasiswa akan ditampilkan untuk pengisian presensi oleh Dosen.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function setSemuaStatus(status) {
        const radios = document.querySelectorAll('.status-radio-' + status);
        radios.forEach(radio => radio.checked = true);
    }
</script>
@endpush
@endsection
