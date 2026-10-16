@props([
    'mobile' => false,
])

@php
    $user = auth()->user();
    $role = $user?->role ?? 'mahasiswa';
    $isStudent = in_array($role, ['siswa', 'mahasiswa']);
    $isLecturer = in_array($role, ['guru', 'dosen']);
    $isAdmin = in_array($role, ['superadmin', 'admin', 'petugas']);

    $linkBase = 'flex items-center gap-2.5 px-3 py-2 min-h-[40px] rounded-xl text-xs font-medium transition-colors select-none';
    $activeClass = 'bg-brand-600 text-white font-semibold shadow-xs';
    $inactiveClass = 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800/80';
    $groupHeaderClass = 'px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block';
    $clickAction = $mobile ? 'onclick=toggleMobileMenu(true)' : '';
@endphp

<nav class="space-y-4">
    {{-- 1. UTAMA --}}
    <div>
        <span class="{{ $groupHeaderClass }}">Utama</span>
        <div class="space-y-0.5 mt-1">
            <a href="{{ route('dashboard') }}" {{ $clickAction }}
               class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            @if($isStudent)
                <a href="{{ route('siakad.presensi.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.presensi.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.presensi.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Presensi Kuliah</span>
                </a>

                <a href="{{ route('siakad.tugas.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.tugas.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.tugas.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Tugas &amp; LMS</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. ROLE-SPECIFIC WORKFLOWS --}}
    @if($isStudent)
        {{-- MAHASISWA: AKADEMIK --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Akademik &amp; Studi</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('siakad.krs.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.krs.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.krs.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Smart KRS</span>
                </a>

                <a href="{{ route('web.nilai.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.nilai.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.nilai.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Nilai &amp; Transkrip</span>
                </a>

                <a href="{{ route('siakad.analytics.performance') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.analytics.performance') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.analytics.performance') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Grafik Performa</span>
                </a>

                <a href="{{ route('siakad.edom.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.edom.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.edom.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span>Evaluasi Dosen (EDOM)</span>
                </a>
            </div>
        </div>

        {{-- MAHASISWA: LAYANAN & KEUANGAN --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Layanan &amp; Keuangan</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('siakad.ukt.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.ukt.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.ukt.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Pembayaran UKT / SPP</span>
                </a>

                <a href="{{ route('siakad.esurat.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.esurat.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.esurat.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>e-Surat Akademik</span>
                </a>

                <a href="{{ route('siakad.fasilitas.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.fasilitas.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.fasilitas.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Peminjaman Fasilitas</span>
                </a>

                <a href="{{ route('siakad.biodata.edit') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.biodata.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.biodata.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Bio Data Mahasiswa</span>
                </a>
            </div>
        </div>

        {{-- MAHASISWA: TINGKAT AKHIR & DBPTA --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Tingkat Akhir &amp; DBPTA</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('siakad.dbpta.proposal.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dbpta.proposal.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dbpta.proposal.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Proposal Skripsi</span>
                </a>

                <a href="{{ route('siakad.dbpta.bimbingan.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dbpta.bimbingan.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dbpta.bimbingan.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Bimbingan Tugas Akhir</span>
                </a>

                <a href="{{ route('siakad.dbpta.sidang.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dbpta.sidang.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dbpta.sidang.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Sidang &amp; Yudisium</span>
                </a>

                <a href="{{ route('siakad.dbpta.pemberkasan.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dbpta.pemberkasan.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dbpta.pemberkasan.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Pemberkasan Ijazah</span>
                </a>

                <a href="{{ route('siakad.kkn.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.kkn.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.kkn.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Kuliah Kerja Nyata (KKN)</span>
                </a>

                <a href="{{ route('siakad.wisuda.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.wisuda.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.wisuda.*') ? 'text-white' : 'text-brand-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                    <span>Pendaftaran Wisuda</span>
                </a>
            </div>
        </div>

    @elseif($isLecturer)
        {{-- DOSEN: PENGAJARAN & KELAS --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Perkuliahan &amp; Pengajaran</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('web.jadwal.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.jadwal.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.jadwal.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Jadwal Mengajar</span>
                </a>

                <a href="{{ route('web.presensi.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.presensi.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.presensi.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Presensi Kelas</span>
                </a>

                <a href="{{ route('web.nilai.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.nilai.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.nilai.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Input Nilai Mahasiswa</span>
                </a>
            </div>
        </div>

        {{-- DOSEN: BIMBINGAN & EVALUASI --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Bimbingan &amp; Evaluasi</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('siakad.dosen.krs.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dosen.krs.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dosen.krs.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Persetujuan KRS PA</span>
                </a>

                <a href="{{ route('siakad.dosen.bap.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.dosen.bap.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('siakad.dosen.bap.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Digital BAP Perkuliahan</span>
                </a>
            </div>
        </div>

    @elseif($isAdmin)
        {{-- ADMIN: PENGGUNA --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Pengguna &amp; Hak Akses</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('web.users.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.users.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Manajemen User</span>
                </a>
            </div>
        </div>

        {{-- ADMIN: AKADEMIK --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Akademik &amp; Perkuliahan</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('web.dosen.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.dosen.*') || request()->routeIs('web.guru.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.dosen.*') || request()->routeIs('web.guru.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Dosen &amp; Pendidik</span>
                </a>

                <a href="{{ route('web.mahasiswa.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.mahasiswa.*') || request()->routeIs('web.siswa.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.mahasiswa.*') || request()->routeIs('web.siswa.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Data Mahasiswa</span>
                </a>

                <a href="{{ route('web.matakuliah.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.matakuliah.*') || request()->routeIs('web.mapel.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.matakuliah.*') || request()->routeIs('web.mapel.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Mata Kuliah</span>
                </a>

                <a href="{{ route('web.kelas.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.kelas.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.kelas.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Kelas Perkuliahan</span>
                </a>

                <a href="{{ route('web.jadwal.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.jadwal.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.jadwal.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Jadwal Perkuliahan</span>
                </a>

                <a href="{{ route('web.nilai.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.nilai.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.nilai.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Nilai &amp; Transkrip</span>
                </a>

                <a href="{{ route('web.presensi.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.presensi.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.presensi.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Presensi Mahasiswa</span>
                </a>

                <a href="{{ route('siakad.baak.ews.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.baak.ews.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>EWS Risiko Drop-Out</span>
                </a>

                <a href="{{ route('siakad.eksekutif.dashboard') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('siakad.eksekutif.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Executive AI Panel</span>
                </a>
            </div>
        </div>

        {{-- ADMIN: KEUANGAN --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Keuangan &amp; UKT</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('web.pembayaran.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.pembayaran.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.pembayaran.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Transaksi Pembayaran</span>
                </a>

                <a href="{{ route('web.ukt.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.ukt.*') || request()->routeIs('web.spp.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.ukt.*') || request()->routeIs('web.spp.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tarif UKT / SPP</span>
                </a>

                <a href="{{ route('web.laporan.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.laporan.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.laporan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan Keuangan</span>
                </a>
            </div>
        </div>

        {{-- ADMIN: SISTEM --}}
        <div>
            <span class="{{ $groupHeaderClass }}">Sistem &amp; Keamanan</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('web.activity-logs.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('web.activity-logs.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('web.activity-logs.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Audit Trail Forensik</span>
                </a>

                <a href="{{ url('/api/docs') }}" target="_blank"
                   class="{{ $linkBase }} text-brand-300 hover:text-white hover:bg-slate-900/90 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4 shrink-0 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Interactive API Console</span>
                </a>
            </div>
        </div>
    @endif

    {{-- 3. PENGATURAN AKUN --}}
    @auth
        <div>
            <span class="{{ $groupHeaderClass }}">Pengaturan</span>
            <div class="space-y-0.5 mt-1">
                <a href="{{ route('profile.index') }}" {{ $clickAction }}
                   class="{{ $linkBase }} {{ request()->routeIs('profile.*') ? $activeClass : $inactiveClass }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('profile.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Ubah Password Akun</span>
                </a>
            </div>
        </div>
    @endauth
</nav>
