@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem (Audit Trail)')

@section('content')
<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900 text-white">Audit Trail</span>
                <span class="text-xs font-semibold text-slate-400">Enterprise Security Log</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Log Aktivitas Sistem</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekam jejak setiap aksi autentikasi, transaksi, dan manipulasi data secara real-time</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold border border-emerald-200 inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Logging Aktif
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('web.activity-logs.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="w-full md:flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi, nama user, atau IP address..."
                    class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="w-full md:w-48">
                <select name="action" class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Aksi --</option>
                    @foreach($actionTypes as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">
                    Filter
                </button>
                @if(request('search') || request('action'))
                    <a href="{{ route('web.activity-logs.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">Waktu</th>
                        <th class="py-3 px-4 font-semibold">Pengguna (User)</th>
                        <th class="py-3 px-4 font-semibold">Jenis Aksi</th>
                        <th class="py-3 px-4 font-semibold">Deskripsi Aktivitas</th>
                        <th class="py-3 px-4 font-semibold text-right">IP & Klien</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        @php
                            $action = $log->action;
                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            if (str_contains($action, 'LOGIN')) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (str_contains($action, 'LOGOUT')) {
                                $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                            } elseif (str_contains($action, 'PEMBAYARAN')) {
                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (str_contains($action, 'SISWA')) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            } elseif (str_contains($action, 'DELETE')) {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                            } elseif (str_contains($action, 'KELAS') || str_contains($action, 'SPP')) {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                <span class="font-mono text-slate-800 font-semibold block">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $log->user->role }} &bull; {{ $log->user->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Tamu</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold border {{ $badgeClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-md">
                                <p class="leading-relaxed">{{ $log->description }}</p>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <span class="font-mono text-[11px] text-slate-600 block">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-[140px] inline-block" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? 'CLI / Browser', 25) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <div class="text-2xl mb-1"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
