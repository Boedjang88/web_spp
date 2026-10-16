@extends('layouts.app')

@section('title', 'Edit Tarif UKT')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.spp.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Data Tarif UKT</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">Edit Tarif UKT</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Tarif UKT</h1>
            <p class="text-xs text-slate-500">Perbarui besaran tarif UKT tahun {{ $spp->tahun }}</p>
        </div>
        <a href="{{ route('web.spp.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <form method="POST" action="{{ route('web.spp.update', $spp->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="tahun" class="block text-xs font-semibold text-slate-700 mb-1">Tahun Akademik / Angkatan <span class="text-rose-500">*</span></label>
                <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $spp->tahun) }}" required
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/50 border @error('tahun') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
                @error('tahun')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nominal" class="block text-xs font-semibold text-slate-700 mb-1">Nominal Biaya Kuliah (Rupiah) <span class="text-rose-500">*</span></label>
                <div class="relative rounded-xl">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-mono text-xs font-bold">
                        Rp
                    </span>
                    <input type="number" id="nominal" name="nominal" value="{{ old('nominal', $spp->nominal) }}" required min="0" step="1000"
                        class="w-full pl-10 pr-3.5 py-2.5 text-xs bg-slate-50/50 border @error('nominal') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono font-bold transition">
                </div>
                @error('nominal')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                <a href="{{ route('web.spp.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-500/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Perbarui Tarif UKT</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
