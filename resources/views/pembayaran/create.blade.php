@extends('layouts.app')

@section('title', 'Entri Pembayaran SPP / UKT')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Transaksi Pembayaran</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Entri Baru</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Entri Pembayaran SPP / UKT</h1>
            <p class="text-xs text-slate-500">Catat penerimaan pembayaran SPP bulanan atau sekaligus multi-bulan</p>
        </div>
        <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.pembayaran.store') }}" class="space-y-4 text-xs" id="pembayaranForm">
            @csrf

            <!-- Pilih Mahasiswa -->
            <div>
                <label for="id_siswa" class="block font-semibold text-slate-700 mb-1">Mahasiswa <span class="text-rose-500">*</span></label>
                <select id="id_siswa" name="id_siswa" required onchange="onSiswaChanged(this)"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border @error('id_siswa') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Cari dan Pilih Mahasiswa --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" 
                            data-nominal="{{ $s->spp?->nominal ?? 0 }}"
                            data-tahun="{{ $s->spp?->tahun ?? date('Y') }}"
                            data-kelas="{{ $s->kelas?->nama_kelas ?? '-' }}"
                            {{ (old('id_siswa', $selectedSiswa?->id) == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} (NIM: {{ $s->nim ?? $s->nisn }}) - Kelas {{ $s->kelas?->nama_kelas }} - UKT Rp {{ number_format($s->spp?->nominal ?? 0, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('id_siswa')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal Bayar -->
                <div>
                    <label for="tgl_bayar" class="block font-semibold text-slate-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" id="tgl_bayar" name="tgl_bayar" value="{{ old('tgl_bayar', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tgl_bayar') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    @error('tgl_bayar')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Dibayar -->
                <div>
                    <label for="tahun_dibayar" class="block font-semibold text-slate-700 mb-1">Tahun Anggaran UKT / Biaya Kuliah <span class="text-rose-500">*</span></label>
                    <input type="number" id="tahun_dibayar" name="tahun_dibayar" value="{{ old('tahun_dibayar', date('Y')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border @error('tahun_dibayar') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                    @error('tahun_dibayar')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pilih Bulan (Multi-Select Checkboxes) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-semibold text-slate-700">Pilih Bulan Pembayaran <span class="text-slate-400 font-normal">(dapat bayar sekaligus multi-bulan)</span> <span class="text-rose-500">*</span></label>
                    <div class="space-x-2">
                        <button type="button" onclick="toggleAllMonths(true)" class="text-[11px] text-indigo-600 hover:text-indigo-800 hover:underline font-semibold">Pilih Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" onclick="toggleAllMonths(false)" class="text-[11px] text-slate-500 hover:text-slate-700 hover:underline font-semibold">Kosongkan</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-slate-50/70 p-3.5 rounded-2xl border border-slate-200">
                    @foreach($daftarBulan as $bln)
                        <label class="flex items-center gap-2 p-2 bg-white rounded-xl border border-slate-200 hover:border-indigo-400 cursor-pointer text-xs select-none transition shadow-sm">
                            <input type="checkbox" name="bulan_dibayar[]" value="{{ $bln }}" onchange="recalculateTotal()"
                                class="month-checkbox rounded text-indigo-600 focus:ring-indigo-500"
                                {{ (is_array(old('bulan_dibayar')) && in_array($bln, old('bulan_dibayar'))) || old('bulan_dibayar') == $bln || (empty(old('bulan_dibayar')) && $bln == 'Juli') ? 'checked' : '' }}>
                            <span class="font-medium text-slate-800">{{ $bln }}</span>
                        </label>
                    @endforeach
                </div>
                @error('bulan_dibayar')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kalkulasi Estimasi Pembayaran Live Card -->
            <div class="p-4 bg-gradient-to-r from-indigo-50/80 to-blue-50/60 border border-indigo-100 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-indigo-700 font-semibold uppercase tracking-wider block">Total Estimasi Pembayaran:</span>
                    <div id="displayTotal" class="text-xl font-black text-indigo-900 mt-0.5">Rp 0</div>
                    <span id="displayCount" class="text-[11px] text-indigo-600 font-medium">1 bulan dipilih</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block text-[10px]">Petugas Loket:</span>
                    <span class="font-bold text-slate-800 text-xs">{{ auth()->user()->name ?? 'Petugas' }}</span>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.pembayaran.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Proses &amp; Simpan Transaksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentNominal = 0;

    function onSiswaChanged(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        if (selected && selected.value) {
            currentNominal = parseInt(selected.getAttribute('data-nominal') || 0);
            const tahun = selected.getAttribute('data-tahun');
            if (tahun) {
                document.getElementById('tahun_dibayar').value = tahun;
            }
        } else {
            currentNominal = 0;
        }
        recalculateTotal();
    }

    function toggleAllMonths(check) {
        document.querySelectorAll('.month-checkbox').forEach(cb => cb.checked = check);
        recalculateTotal();
    }

    function recalculateTotal() {
        const checkedCount = document.querySelectorAll('.month-checkbox:checked').length;
        const totalRupiah = checkedCount * currentNominal;

        document.getElementById('displayTotal').innerText = 'Rp ' + totalRupiah.toLocaleString('id-ID');
        document.getElementById('displayCount').innerText = checkedCount + ' bulan dipilih (@ Rp ' + currentNominal.toLocaleString('id-ID') + '/bln)';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const siswaSelect = document.getElementById('id_siswa');
        if (siswaSelect) onSiswaChanged(siswaSelect);
    });
</script>
@endpush
@endsection
