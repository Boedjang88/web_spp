@extends('layouts.app')

@section('title', 'Edit Tarif SPP')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Tarif SPP</h1>
            <p class="text-xs text-slate-500">Perbarui besaran tarif SPP tahun {{ $spp->tahun }}</p>
        </div>
        <a href="{{ route('web.spp.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.spp.update', $spp->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="tahun" class="block text-xs font-semibold text-slate-700 mb-1">Tahun Angkatan / Ajaran</label>
                <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $spp->tahun) }}" required
                    class="w-full px-3.5 py-2 text-xs border @error('tahun') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tahun')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nominal" class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rupiah per Bulan)</label>
                <input type="number" id="nominal" name="nominal" value="{{ old('nominal', $spp->nominal) }}" required min="0" step="1000"
                    class="w-full px-3.5 py-2 text-xs border @error('nominal') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nominal')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <a href="{{ route('web.spp.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">Perbarui Tarif SPP</button>
            </div>
        </form>
    </div>
</div>
@endsection
