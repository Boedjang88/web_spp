<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cek Tagihan SPP - SMK Merdeka Belajar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Font Biar Lebih Modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-3 md:p-6">

    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div class="bg-blue-600 p-5 md:p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full -translate-x-10 -translate-y-10"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full translate-x-5 translate-y-5"></div>
            
            <h1 class="text-xl md:text-3xl font-bold text-white tracking-wide uppercase">Portal SPP Online</h1>
            <p class="text-blue-100 text-xs md:text-base mt-1">SMK Merdeka Belajar</p>
        </div>

        <div class="p-5 md:p-8">
            <form action="{{ route('cek.search') }}" method="POST" class="mb-6 md:mb-8">
                @csrf
                <label class="block text-gray-700 text-sm font-semibold mb-2">Cari Siswa</label>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="number" name="nisn" placeholder="Masukkan NISN..." required
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm text-lg"
                            value="{{ request('nisn') }}">
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition shadow-md active:scale-95 flex justify-center items-center">
                        Cek Data
                    </button>
                </div>

                @if(session('error'))
                    <div class="mt-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-sm shadow-sm" role="alert">
                        <p class="font-bold">Oops!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
            </form>

            <div class="border-t border-gray-100 my-6"></div>

            @if(isset($siswa))
                @php $info = $siswa->info_tunggakan; @endphp

                <div class="animate-fade-in space-y-6">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ $siswa->nama }}</h2>
                            <div class="text-gray-500 text-sm mt-1 flex flex-col sm:flex-row sm:gap-4">
                                <span><span class="font-semibold text-gray-700">NISN:</span> {{ $siswa->nisn }}</span>
                                <span class="hidden sm:inline text-gray-300">|</span>
                                <span>{{ $siswa->kelas->nama_kelas }} ({{ $siswa->kelas->kompetensi_keahlian }})</span>
                            </div>
                        </div>
                        
                        @if($info['total_bulan'] == 0)
                            <div class="flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                LUNAS
                            </div>
                        @else
                            <div class="flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-full font-bold text-sm shadow-sm animate-pulse">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Nunggak {{ $info['total_bulan'] }} Bulan
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-white to-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden group">
                            <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-110 transition duration-300">
                                <svg class="w-16 h-16 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Total Tunggakan</h3>
                            <p class="text-2xl md:text-3xl font-extrabold {{ $info['total_rupiah'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($info['total_rupiah'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2">Tarif SPP: Rp {{ number_format($siswa->spp->nominal, 0, ',', '.') }}/bulan</p>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-3">Pembayaran Terakhir</h3>
                            @if($siswa->pembayarans->count() > 0)
                                @php $last = $siswa->pembayarans->last(); @endphp
                                <div class="flex items-start gap-3">
                                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $last->bulan_dibayar }} {{ $last->tahun_dibayar }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Diterima oleh: {{ $last->petugas->name ?? 'Admin' }}<br>
                                            Tgl: {{ \Carbon\Carbon::parse($last->tgl_bayar)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-400">
                                    <p class="text-sm italic">Belum ada data pembayaran.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($info['total_bulan'] > 0)
                        <div class="bg-red-50 rounded-xl p-5 border border-red-100">
                            <h3 class="text-red-800 font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Rincian Bulan Belum Lunas:
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($info['list_bulan'] as $bulan)
                                    <span class="bg-white text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold shadow-sm flex-grow sm:flex-grow-0 text-center">
                                        {{ $bulan }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                         <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                            <div class="inline-block p-3 bg-green-100 rounded-full text-green-600 mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-green-800 font-bold text-lg">Terima Kasih!</h3>
                            <p class="text-green-600 text-sm mt-1">Pembayaran SPP ananda sudah lunas sampai saat ini.</p>
                        </div>
                    @endif

                </div>
            @else
                @if(!session('error'))
                    <div class="text-center py-10 opacity-60">
                        <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p class="text-gray-500 font-medium">Masukkan NISN siswa untuk melihat data.</p>
                    </div>
                @endif
            @endif

        </div>
        
        <div class="bg-gray-50 p-4 text-center text-gray-400 text-xs border-t border-gray-100">
            &copy; {{ date('Y') }} Sistem Informasi SPP Sekolah
        </div>
    </div>

</body>
</html>