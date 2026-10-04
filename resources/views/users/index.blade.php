@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700">
                    Sistem Hak Akses RBAC 4-Level
                </span>
                <span class="text-xs text-slate-400">Total {{ $stats['total'] }} Akun</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun Super Admin, Admin Tata Usaha, Dewan Guru, dan Siswa.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.users.create') }}" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
                <span>➕</span> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Role Distribution Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-purple-600">Super Admin</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['superadmin'] }}</div>
            <p class="text-[10px] text-slate-400">Akses Penuh Sistem</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Admin / TU</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['admin'] }}</div>
            <p class="text-[10px] text-slate-400">Kelola Akademik & SPP</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Dewan Guru</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['guru'] }}</div>
            <p class="text-[10px] text-slate-400">Input Nilai & Presensi</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Siswa Aktif</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['siswa'] }}</div>
            <p class="text-[10px] text-slate-400">Portal Rapor & SPP</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('web.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengguna atau email..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
            </div>
            <div class="sm:col-span-4">
                <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
                    <option value="">Semua Tingkatan Role</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin / Petugas TU</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Dewan Guru</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('web.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-3 py-2.5 rounded-xl text-xs transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Role Akses</th>
                        <th class="px-6 py-4">Terkait Data Master</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-black flex items-center justify-center text-xs border border-slate-200">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->role === 'superadmin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700 border border-purple-200">
                                        👑 Super Admin
                                    </span>
                                @elseif($u->role === 'admin' || $u->role === 'petugas')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                        💼 Admin TU
                                    </span>
                                @elseif($u->role === 'guru')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        👨‍🏫 Dewan Guru
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200">
                                        🎓 Siswa
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($u->role === 'guru' && $u->guru)
                                    <div class="font-semibold text-slate-800">{{ $u->guru->nama_guru }}</div>
                                    <div class="text-[10px] text-slate-400">NIP: {{ $u->guru->nip }}</div>
                                @elseif($u->role === 'siswa' && $u->siswa)
                                    <div class="font-semibold text-slate-800">{{ $u->siswa->nama }}</div>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $u->siswa->nisn }} ({{ $u->siswa->kelas->nama_kelas ?? '-' }})</div>
                                @else
                                    <span class="text-slate-400 italic">User Sistem Mandiri</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active ?? true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-[11px]">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('web.users.edit', $u->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Pengguna">
                                        ✏️
                                    </a>
                                    @if($u->id !== auth()->id() && !($u->role === 'superadmin' && $stats['superadmin'] <= 1))
                                        <form action="{{ route('web.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun pengguna {{ $u->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Pengguna">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">👤</div>
                                Tidak ada data pengguna yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
