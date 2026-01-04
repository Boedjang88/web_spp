<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas'; // Sesuaikan nama tabel di Postgres lu
    protected $fillable = ['nisn', 'nis', 'nama', 'id_kelas', 'alamat', 'no_telp', 'id_spp'];

    // INI WAJIB ADA BIAR DROPDOWN MUNCUL
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function getInfoTunggakanAttribute()
    {
        // 1. Ambil Data SPP Siswa
        $spp = $this->spp;
        if (!$spp) return ['total_bulan' => 0, 'total_rupiah' => 0, 'list_bulan' => []];

        // 2. Tentukan Rentang Tahun Ajaran
        // Misal tahun SPP = 2025, berarti periode: Juli 2025 - Juni 2026
        $tahunMulai = (int) $spp->tahun;
        $tahunSelesai = $tahunMulai + 1;

        // 3. Bikin Daftar 12 Bulan Sesuai Tahun Ajaran
        // Format: [Nama Bulan, Tahunnya, Angka Bulan Buat Cek]
        $kalenderSPP = [
            ['nama' => 'Juli',      'tahun' => $tahunMulai,   'bulan_angka' => 7],
            ['nama' => 'Agustus',   'tahun' => $tahunMulai,   'bulan_angka' => 8],
            ['nama' => 'September', 'tahun' => $tahunMulai,   'bulan_angka' => 9],
            ['nama' => 'Oktober',   'tahun' => $tahunMulai,   'bulan_angka' => 10],
            ['nama' => 'November',  'tahun' => $tahunMulai,   'bulan_angka' => 11],
            ['nama' => 'Desember',  'tahun' => $tahunMulai,   'bulan_angka' => 12],
            ['nama' => 'Januari',   'tahun' => $tahunSelesai, 'bulan_angka' => 1],
            ['nama' => 'Februari',  'tahun' => $tahunSelesai, 'bulan_angka' => 2],
            ['nama' => 'Maret',     'tahun' => $tahunSelesai, 'bulan_angka' => 3],
            ['nama' => 'April',     'tahun' => $tahunSelesai, 'bulan_angka' => 4],
            ['nama' => 'Mei',       'tahun' => $tahunSelesai, 'bulan_angka' => 5],
            ['nama' => 'Juni',      'tahun' => $tahunSelesai, 'bulan_angka' => 6],
        ];

        // 4. Ambil Data Pembayaran Siswa Ini dari Database
        // Kita ambil array format "Bulan-Tahun" biar gampang dicocokin
        // Contoh data: ["Juli-2025", "Agustus-2025"]
        $pembayaranDB = $this->hasMany(Pembayaran::class, 'id_siswa')
            ->where('id_spp', $spp->id)
            ->get()
            ->map(function ($bayar) {
                return $bayar->bulan_dibayar . '-' . $bayar->tahun_dibayar;
            })->toArray();

        // 5. LOGIKA UTAMA: Loop Kalender vs Hari Ini
        $listNunggak = [];
        $sekarang = now(); // Waktu Real-time Server (Januari 2026)

        foreach ($kalenderSPP as $item) {
            // Bikin tanggal virtual buat bulan yg lagi dicek (tgl 1 bulan tsb)
            $tanggalCek = \Carbon\Carbon::createFromDate($item['tahun'], $item['bulan_angka'], 1);

            // A. Cek Apakah Bulan ini SUDAH JATUH TEMPO?
            // (Apakah tanggal bulan ini <= hari ini?)
            // Pakai startOfMonth() biar aman perbandingannya
            if ($tanggalCek->startOfMonth() <= $sekarang->startOfMonth()) {
                
                // B. Kalau Sudah Jatuh Tempo, Cek Apakah SUDAH BAYAR?
                // Kita gabungin nama bulan & tahun buat kunci pencarian (misal: "Januari-2026")
                $kunciCek = $item['nama'] . '-' . $item['tahun'];

                if (!in_array($kunciCek, $pembayaranDB)) {
                    // Kalau gak ada di database, catat sebagai NUNGGAK
                    $listNunggak[] = $item['nama']; // Cukup simpan nama bulannya
                }
            }
        }

        // 6. Hitung Total Duit
        $totalNunggak = count($listNunggak) * $spp->nominal;

        return [
            'total_bulan' => count($listNunggak),
            'total_rupiah' => $totalNunggak,
            'list_bulan' => $listNunggak,
     
   ];
    }

    public function pembayarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_siswa');
    }
}