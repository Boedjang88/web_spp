@extends('layouts.app')

@section('title', 'Analytics & Grafik Prestasi Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-soft flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-3 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-bold rounded-full uppercase">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Performance Analytics
                </span>
                <span class="text-xs text-slate-400 font-mono">{{ $siswa?->nama }} (NIM: {{ $siswa?->nisn ?: $siswa?->nis }})</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Performa Indeks Prestasi & Capaian SKS</h1>
            <p class="text-xs text-slate-500 mt-0.5">Visualisasi tren capaian IPS/IPK semester dan ketercapaian syarat kelulusan.</p>
        </div>

        <div class="flex items-center gap-4 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IPK Kumulatif</span>
                <span class="text-xl font-black text-emerald-600">3.82 <span class="text-xs font-normal text-slate-400">/ 4.00</span></span>
            </div>
            <div class="w-px h-8 bg-slate-200"></div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total SKS Lulus</span>
                <span class="text-xl font-black text-indigo-600">112 <span class="text-xs font-normal text-slate-400">/ 144</span></span>
            </div>
        </div>
    </div>

    <!-- Responsive SVG GPA Line & Bar Chart -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg> Tren Indeks Prestasi Semester (IPS)</h2>
                <span class="text-[11px] text-slate-400">Dihitung otomatis per semester berdasarkan bobot mutu mata kuliah</span>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-500"></span> <span>IPS Semester</span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> <span>Target Kelulusan (3.00)</span></div>
            </div>
        </div>

        <!-- SVG Container -->
        <div class="w-full aspect-[21/9] sm:aspect-[24/8] bg-slate-50/50 rounded-2xl border border-slate-200/60 p-4 flex items-center justify-center">
            <svg class="w-full h-full" viewBox="0 0 800 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Grid Lines -->
                <line x1="60" y1="40" x2="760" y2="40" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="4 4"/>
                <text x="35" y="45" fill="#94a3b8" font-size="10" font-family="monospace">4.00</text>

                <line x1="60" y1="90" x2="760" y2="90" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="4 4"/>
                <text x="35" y="95" fill="#94a3b8" font-size="10" font-family="monospace">3.00</text>

                <line x1="60" y1="140" x2="760" y2="140" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="4 4"/>
                <text x="35" y="145" fill="#94a3b8" font-size="10" font-family="monospace">2.00</text>

                <line x1="60" y1="190" x2="760" y2="190" stroke="#cbd5e1" stroke-width="1.5"/>
                <text x="35" y="195" fill="#94a3b8" font-size="10" font-family="monospace">0.00</text>

                <!-- Benchmark Line (3.00 Target) -->
                <line x1="60" y1="90" x2="760" y2="90" stroke="#10b981" stroke-width="2" stroke-dasharray="6 4" opacity="0.7"/>

                <!-- Area Fill -->
                <polygon points="120,65 240,55 360,60 480,45 600,48 720,40 720,190 120,190" fill="url(#gradientGpa)" opacity="0.15"/>

                <!-- Trend Line -->
                <polyline points="120,65 240,55 360,60 480,45 600,48 720,40" fill="none" stroke="#6366f1" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>

                <!-- Data Nodes -->
                <!-- Sem 1 (3.50 -> y:65) -->
                <circle cx="120" cy="65" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="120" y="50" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">3.50</text>
                <text x="120" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 1</text>

                <!-- Sem 2 (3.70 -> y:55) -->
                <circle cx="240" cy="55" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="240" y="40" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">3.70</text>
                <text x="240" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 2</text>

                <!-- Sem 3 (3.60 -> y:60) -->
                <circle cx="360" cy="60" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="360" y="45" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">3.60</text>
                <text x="360" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 3</text>

                <!-- Sem 4 (3.90 -> y:45) -->
                <circle cx="480" cy="45" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="480" y="30" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">3.90</text>
                <text x="480" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 4</text>

                <!-- Sem 5 (3.85 -> y:48) -->
                <circle cx="600" cy="48" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="600" y="33" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">3.85</text>
                <text x="600" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 5</text>

                <!-- Sem 6 (4.00 -> y:40) -->
                <circle cx="720" cy="40" r="5" fill="#ffffff" stroke="#6366f1" stroke-width="3"/>
                <text x="720" y="25" text-anchor="middle" fill="#4338ca" font-size="10" font-weight="bold">4.00</text>
                <text x="720" y="210" text-anchor="middle" fill="#64748b" font-size="10">Sem 6</text>

                <defs>
                    <linearGradient id="gradientGpa" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#6366f1"/>
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>

</div>
@endsection
