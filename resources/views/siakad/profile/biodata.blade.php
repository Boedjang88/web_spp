@extends('layouts.app')

@section('title', 'Lengkapi Data Diri Mahasiswa')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-brand-900 p-6 sm:p-8 rounded-2xl border border-slate-800 text-white shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-brand-200 border border-white/10 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>UU PDP Compliance &amp; AES-256 Encryption</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Lengkapi Biodata Mahasiswa</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">Perbarui data diri, kontak domisili, data orang tua/wali, dan riwayat pendidikan Anda.</p>
        </div>
        <div class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right">
            <div class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">NIM Mahasiswa</div>
            <div class="text-lg font-mono font-bold text-brand-300">{{ $mahasiswa->nim }}</div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-bold mb-1">Terdapat kesalahan input:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('siakad.biodata.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Identitas Pribadi -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>1. Data Pribadi Mahasiswa &amp; Foto Profil</span>
            </div>

            <!-- Upload Foto Profil (Avatar) -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 mb-2">
                <label class="block text-xs font-semibold text-slate-700 mb-2">Foto Profil (Avatar)</label>
                <div class="flex items-center gap-4">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-brand-100 text-brand-700 font-bold flex items-center justify-center text-base flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="avatar" accept="image/*"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG, PNG, WEBP (Maksimal 2MB). Foto ini akan tampil pada User Profile Card &amp; Kartu Mahasiswa.</span>
                    </div>
                </div>
                @error('avatar')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Sesuai KTP/Ijazah</label>
                    <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        NIK (Nomor Induk Kependudukan)
                        <span class="text-[10px] text-emerald-600 font-bold ml-1">Terenkripsi Database</span>
                    </label>
                    <input type="text" name="nik" value="{{ old('nik', $mahasiswa->nik) }}" placeholder="16 Digit NIK"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-mono font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                        <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Agama</label>
                    <input type="text" name="agama" value="{{ old('agama', $mahasiswa->agama ?? 'Islam') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $mahasiswa->no_telp) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Pribadi</label>
                    <input type="email" name="email_pribadi" value="{{ old('email_pribadi', $mahasiswa->email_pribadi) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Alamat Domisili -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <span>2. Alamat Domisili &amp; Tempat Tinggal</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap (Jalan / Nomor / Blok)</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $mahasiswa->alamat) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">RT</label>
                    <input type="text" name="rt" value="{{ old('rt', $mahasiswa->rt) }}" placeholder="001"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">RW</label>
                    <input type="text" name="rw" value="{{ old('rw', $mahasiswa->rw) }}" placeholder="005"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $mahasiswa->kode_pos) }}" placeholder="40123"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kelurahan / Desa</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $mahasiswa->kelurahan) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $mahasiswa->kecamatan) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kota / Kabupaten</label>
                    <input type="text" name="kota" value="{{ old('kota', $mahasiswa->kota) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>
            </div>
        </div>

        <!-- Bagian 3: Orang Tua / Wali -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>3. Data Orang Tua &amp; Wali Mahasiswa</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Ayah</label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $mahasiswa->nama_ayah) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Ibu Kandung
                        <span class="text-[10px] text-emerald-600 font-bold ml-1">Terenkripsi Database</span>
                    </label>
                    <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung', $mahasiswa->nama_ibu_kandung) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Ayah</label>
                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $mahasiswa->pekerjaan_ayah) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Ibu</label>
                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $mahasiswa->pekerjaan_ibu) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penghasilan Orang Tua (Per Bulan)</label>
                    <select name="penghasilan_ortu" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                        <option value="< 2.000.000" {{ old('penghasilan_ortu', $mahasiswa->penghasilan_ortu) === '< 2.000.000' ? 'selected' : '' }}>Di bawah Rp 2.000.000</option>
                        <option value="2.000.000 - 5.000.000" {{ old('penghasilan_ortu', $mahasiswa->penghasilan_ortu) === '2.000.000 - 5.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                        <option value="5.000.000 - 10.000.000" {{ old('penghasilan_ortu', $mahasiswa->penghasilan_ortu) === '5.000.000 - 10.000.000' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                        <option value="> 10.000.000" {{ old('penghasilan_ortu', $mahasiswa->penghasilan_ortu) === '> 10.000.000' ? 'selected' : '' }}>Di atas Rp 10.000.000</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        No. HP / Kontak Wali
                        <span class="text-[10px] text-emerald-600 font-bold ml-1">Terenkripsi Database</span>
                    </label>
                    <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $mahasiswa->no_hp_wali) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>
            </div>
        </div>

        <!-- Bagian 4: Riwayat Pendidikan & PDP -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                <span>4. Asal Pendidikan &amp; Perlindungan Data Pribadi (UU PDP)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Asal Sekolah (SMA/SMK)</label>
                    <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $mahasiswa->asal_sekolah) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Lulus</label>
                    <input type="text" name="tahun_lulus_sekolah" value="{{ old('tahun_lulus_sekolah', $mahasiswa->tahun_lulus_sekolah) }}" placeholder="2023"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Ijazah SMA/SMK</label>
                    <input type="text" name="nomor_ijazah_sekolah" value="{{ old('nomor_ijazah_sekolah', $mahasiswa->nomor_ijazah_sekolah) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-brand-500 font-medium">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 flex items-center justify-between">
                <span class="flex items-center gap-1.5 font-medium">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Status Persetujuan UU PDP: <strong class="text-slate-900">{{ $mahasiswa->consent_pdp_at ? 'Aktif Disetujui' : 'Otomatis Disetujui Saat Simpan' }}</strong></span>
                </span>
                <span class="text-slate-400 font-mono">{{ $mahasiswa->consent_pdp_at ? $mahasiswa->consent_pdp_at->format('d/m/Y H:i') : now()->format('d/m/Y') }}</span>
            </div>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Simpan &amp; Perbarui Biodata Mahasiswa</span>
        </button>
    </form>
</div>
@endsection
