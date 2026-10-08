@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#1c2541] p-6 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Sistem Hak Akses RBAC 4-Level
                </span>
                <span class="text-xs text-slate-400">Total {{ $stats['total'] }} Akun</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola akun Super Admin, Admin BAAK / TU, Dosen &amp; Pendidik, dan Mahasiswa.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.users.create') }}" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition inline-flex items-center gap-1.5">
                <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Role Distribution Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1c2541] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Super Admin</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">{{ $stats['superadmin'] }}</div>
            <p class="text-[10px] text-slate-400">Akses Penuh Sistem</p>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Admin BAAK / TU</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">{{ $stats['admin'] }}</div>
            <p class="text-[10px] text-slate-400">Kelola Akademik & UKT</p>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Dosen Pengajar</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">{{ $stats['dosen'] }}</div>
            <p class="text-[10px] text-slate-400">Nilai, BAP & Presensi</p>
        </div>
        <div class="bg-white dark:bg-[#1c2541] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Mahasiswa</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">{{ $stats['mahasiswa'] }}</div>
            <p class="text-[10px] text-slate-400">Portal Smart KRS & UKT</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-[#1c2541] p-4 rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm">
        <form method="GET" action="{{ route('web.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengguna atau email..."
                    class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
            </div>
            <div class="sm:col-span-4">
                <select name="role" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    <option value="">Semua Tingkatan Role</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin BAAK / TU</option>
                    <option value="dosen" {{ in_array(request('role'), ['dosen', 'guru']) ? 'selected' : '' }}>Dosen Pengajar</option>
                    <option value="mahasiswa" {{ in_array(request('role'), ['mahasiswa', 'siswa']) ? 'selected' : '' }}>Mahasiswa</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('web.users.index') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold px-3 py-2.5 rounded-xl text-xs transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider">
                        <th class="px-6 py-4 border-r border-slate-200 dark:border-slate-700">Pengguna</th>
                        <th class="px-6 py-4 border-r border-slate-200 dark:border-slate-700">Role Akses</th>
                        <th class="px-6 py-4 border-r border-slate-200 dark:border-slate-700">Terkait Data Master</th>
                        <th class="px-6 py-4 border-r border-slate-200 dark:border-slate-700">Status Akun</th>
                        <th class="px-6 py-4 border-r border-slate-200 dark:border-slate-700">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-blue-900/20 transition">
                            <td class="px-6 py-4 border-r border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black flex items-center justify-center text-xs border border-slate-200 dark:border-slate-700">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 border-r border-slate-100 dark:border-slate-800">
                                @if(in_array($u->role, ['superadmin', 'super_admin']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase tracking-wider bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        Super Admin
                                    </span>
                                @elseif(in_array($u->role, ['admin', 'petugas', 'baak']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase tracking-wider bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Admin BAAK / TU
                                    </span>
                                @elseif(in_array($u->role, ['dosen', 'guru', 'lecturer']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Dosen Pengajar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase tracking-wider bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Mahasiswa
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-r border-slate-100 dark:border-slate-800">
                                @if($u->isDosen() && ($u->dosen || $u->guru))
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $u->dosen?->nama_dosen ?? $u->guru?->nama_guru }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">NIDN/NIP: {{ $u->dosen?->nidn ?? $u->dosen?->nip ?? $u->guru?->nip }}</div>
                                @elseif($u->isMahasiswa() && ($u->mahasiswa || $u->siswa))
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $u->mahasiswa?->nama ?? $u->siswa?->nama }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">NIM: {{ $u->mahasiswa?->nim ?? $u->siswa?->nisn }} ({{ $u->siswa?->kelas?->nama_kelas ?? 'TI-2024-A' }})</div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Akun Akses Mandiri</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-r border-slate-100 dark:border-slate-800">
                                @if($u->is_active ?? true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-[11px] border-r border-slate-100 dark:border-slate-800">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('web.users.edit', $u->id) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-bold text-xs">Edit</a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('web.users.destroy', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline font-bold text-xs">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-mono">Tidak ada pengguna yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
