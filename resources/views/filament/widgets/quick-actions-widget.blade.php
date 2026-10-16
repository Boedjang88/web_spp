<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2 font-semibold text-gray-950 dark:text-white">
                <x-filament::icon
                    icon="heroicon-o-bolt"
                    class="h-5 w-5 text-primary-600 dark:text-primary-400"
                />
                <span>Aksi Cepat Operasional (Quick Actions)</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Pintasan navigasi langsung untuk tugas administratif prioritas tinggi.
        </x-slot>

        <div x-data="{ tab: 'akademik' }" class="space-y-4">
            <!-- Navigation Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-800 text-xs font-semibold">
                <button
                    type="button"
                    @click="tab = 'akademik'"
                    :class="tab === 'akademik' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="px-4 py-2.5 transition flex items-center gap-1.5 focus:outline-none"
                >
                    <x-filament::icon icon="heroicon-o-academic-cap" class="h-4 w-4" />
                    <span>Akademik</span>
                </button>
                <button
                    type="button"
                    @click="tab = 'keuangan'"
                    :class="tab === 'keuangan' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="px-4 py-2.5 transition flex items-center gap-1.5 focus:outline-none"
                >
                    <x-filament::icon icon="heroicon-o-banknotes" class="h-4 w-4" />
                    <span>Keuangan</span>
                </button>
                <button
                    type="button"
                    @click="tab = 'sistem'"
                    :class="tab === 'sistem' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="px-4 py-2.5 transition flex items-center gap-1.5 focus:outline-none"
                >
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-4 w-4" />
                    <span>Pengaturan</span>
                </button>
            </div>

            <!-- Tab 1: Akademik -->
            <div x-show="tab === 'akademik'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <a
                    href="{{ \App\Filament\Resources\SiswaResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-user-plus" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">Tambah Mahasiswa</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Registrasi akun mahasiswa baru</div>
                    </div>
                </a>

                <a
                    href="{{ \App\Filament\Resources\GuruResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Tambah Dosen</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Input data tenaga pendidik</div>
                    </div>
                </a>

                <a
                    href="{{ \App\Filament\Resources\KelasResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-sky-500 dark:hover:border-sky-500 hover:bg-sky-50/50 dark:hover:bg-sky-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-sky-600 dark:group-hover:text-sky-400">Buat Kelas Baru</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Alokasikan rombongan belajar</div>
                    </div>
                </a>

                <a
                    href="{{ \App\Filament\Resources\MataKuliahResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-amber-500 dark:hover:border-amber-500 hover:bg-amber-50/50 dark:hover:bg-amber-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-book-open" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-amber-600 dark:group-hover:text-amber-400">Mata Kuliah Baru</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Atur kurikulum & bobot SKS</div>
                    </div>
                </a>
            </div>

            <!-- Tab 2: Keuangan -->
            <div x-show="tab === 'keuangan'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" style="display: none;">
                <a
                    href="{{ \App\Filament\Resources\PembayaranResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Catat Pembayaran</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Verifikasi setor tunai & kwitansi</div>
                    </div>
                </a>

                <a
                    href="{{ \App\Filament\Resources\SppResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-currency-dollar" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">Atur Tarif UKT</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Matriks nominal tahun angkatan</div>
                    </div>
                </a>

                <a
                    href="{{ route('cek.index') }}"
                    target="_blank"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Cek Tagihan Publik</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Portal verifikasi tagihan mandiri</div>
                    </div>
                </a>

                <a
                    href="{{ route('web.laporan.index') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-rose-500 dark:hover:border-rose-500 hover:bg-rose-50/50 dark:hover:bg-rose-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-document-chart-bar" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-rose-600 dark:group-hover:text-rose-400">Rekap Laporan</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Ekspor laporan keuangan berkala</div>
                    </div>
                </a>
            </div>

            <!-- Tab 3: Pengaturan -->
            <div x-show="tab === 'sistem'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" style="display: none;">
                <a
                    href="{{ \App\Filament\Resources\UserResource::getUrl('create') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-shield-check" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">Tambah Operator</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Kredensial staf & administrator</div>
                    </div>
                </a>

                <a
                    href="{{ route('siakad.eksekutif.dashboard') }}"
                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-purple-500 dark:hover:border-purple-500 hover:bg-purple-50/50 dark:hover:bg-purple-950/30 transition group"
                >
                    <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 group-hover:scale-105 transition">
                        <x-filament::icon icon="heroicon-o-cpu-chip" class="h-4 w-4" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400">Executive AI Panel</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Prediksi akreditasi & analitik</div>
                    </div>
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
