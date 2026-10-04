@extends('layouts.app')

@section('title', 'Presensi Kehadiran Mahasiswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Kehadiran Perkuliahan</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Presensi Kehadiran Mahasiswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat kehadiran mahasiswa per kelas kuliah secara presisi (Hadir, Izin, Sakit, Alpa)</p>
        </div>
    </div>

    <!-- Class & Date Selector Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-5">
        <form method="GET" action="{{ route('web.presensi.index') }}" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
            <div class="w-full sm:w-60">
                <label class="block font-semibold text-slate-700 mb-1">Pilih Kelas Kuliah</label>
                <select name="id_kelas" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Kelas Kuliah --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Perkuliahan</label>
                <input type="date" name="tanggal" value="{{ $selectedTanggal }}" required
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
            </div>

            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-600/20 transition">
                Buka Lembar Presensi
            </button>
        </form>
    </div>

    @if($selectedKelasId)
        <!-- Attendance Sheet Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
            
            <div class="p-4 bg-slate-50/50 border-b border-slate-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <span class="font-bold text-slate-900 text-sm">Lembar Kehadiran Mahasiswa ({{ $siswas->count() }} Mahasiswa)</span>
                    <p class="text-slate-500 text-xs mt-0.5">Tanggal: <strong class="font-mono text-slate-700">{{ \Carbon\Carbon::parse($selectedTanggal)->translatedFormat('l, d F Y') }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setSemuaStatus('Hadir')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/60 rounded-xl text-xs font-bold transition flex items-center gap-1">
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
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">
                                <th class="py-3.5 px-4 font-semibold">No</th>
                                <th class="py-3.5 px-4 font-semibold">NIM / NISN</th>
                                <th class="py-3.5 px-4 font-semibold">Nama Mahasiswa</th>
                                <th class="py-3.5 px-4 font-semibold text-center">Status Kehadiran</th>
                                <th class="py-3.5 px-4 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswas as $idx => $s)
                                @php
                                    $existingPresensi = $s->presensis->first();
                                    $currentStatus = $existingPresensi ? $existingPresensi->status : 'Hadir';
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4 font-mono font-semibold text-slate-600">{{ $s->nim ?? $s->nisn }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $s->nama }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <input type="hidden" name="presensi[{{ $idx }}][id_siswa]" value="{{ $s->id }}">
                                        <div class="inline-flex items-center gap-3">
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Hadir" {{ $currentStatus == 'Hadir' ? 'checked' : '' }} class="status-radio-Hadir text-emerald-600 focus:ring-emerald-500">
                                                <span class="font-bold text-emerald-700">Hadir</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Izin" {{ $currentStatus == 'Izin' ? 'checked' : '' }} class="status-radio-Izin text-blue-600 focus:ring-blue-500">
                                                <span class="font-bold text-blue-700">Izin</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Sakit" {{ $currentStatus == 'Sakit' ? 'checked' : '' }} class="status-radio-Sakit text-amber-600 focus:ring-amber-500">
                                                <span class="font-bold text-amber-700">Sakit</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Alpa" {{ $currentStatus == 'Alpa' ? 'checked' : '' }} class="status-radio-Alpa text-rose-600 focus:ring-rose-500">
                                                <span class="font-bold text-rose-700">Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="presensi[{{ $idx }}][keterangan]" value="{{ $existingPresensi?->keterangan }}" placeholder="Catatan opsional..."
                                            class="w-full px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400">Belum ada mahasiswa di kelas perkuliahan yang dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswas->count() > 0)
                    <div class="p-4 bg-slate-50/50 border-t border-slate-200/80 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Simpan Presensi Perkuliahan</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="p-12 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mx-auto mb-3 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-600">Silakan pilih kelas kuliah dan tanggal perkuliahan di atas</p>
            <p class="text-xs text-slate-400 mt-1">Daftar mahasiswa akan ditampilkan untuk pengisian presensi perkuliahan.</p>
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
