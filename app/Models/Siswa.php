<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';
    protected $fillable = [
        'nisn',
        'nis',
        'nik',
        'nama',
        'id_kelas',
        'alamat',
        'nama_ibu_kandung',
        'no_telp',
        'no_hp_wali',
        'id_spp',
        'status_kelulusan',
        'tgl_kelulusan',
        'nomor_ijazah',
        'total_skpi_points',
        'consent_pdp_at',
        'consent_pdp_ip',
    ];

    protected $casts = [
        'nik' => 'encrypted',
        'nama_ibu_kandung' => 'encrypted',
        'no_hp_wali' => 'encrypted',
        'consent_pdp_at' => 'datetime',
        'tgl_kelulusan' => 'datetime',
        'total_skpi_points' => 'integer',
    ];

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
        // 0. Cek Verifikasi Lunas Terpusat (TagihanUKT / FinancialClearance)
        $hasLunasUkt = TagihanUkt::where('id_mahasiswa', $this->id)
            ->where(function ($q) {
                $q->where('status_pembayaran', 'Lunas')
                  ->orWhereColumn('total_sudah_bayar', '>=', 'total_harus_bayar');
            })->exists();

        $hasClearance = FinancialClearance::where('id_siswa', $this->id)
            ->where(function ($q) {
                $q->where('is_cleared', true)
                  ->orWhere('is_krs_unlocked', true)
                  ->orWhere('status', 'CLEARED');
            })->exists();

        if ($hasLunasUkt || $hasClearance) {
            return ['total_bulan' => 0, 'total_rupiah' => 0, 'list_bulan' => []];
        }

        // 1. Ambil Data SPP Siswa
        $spp = $this->spp;
        if (!$spp) return ['total_bulan' => 0, 'total_rupiah' => 0, 'list_bulan' => []];

        // 2. Tentukan Rentang Tahun Ajaran
        $tahunMulai = (int) $spp->tahun;
        $tahunSelesai = $tahunMulai + 1;

        // 3. Bikin Daftar 12 Bulan Sesuai Tahun Ajaran
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

        // 4. Ambil Data Pembayaran Siswa Ini (SPP + UKT)
        $pembayaranDB = $this->hasMany(Pembayaran::class, 'id_siswa')
            ->where('id_spp', $spp->id)
            ->get()
            ->map(function ($bayar) {
                return $bayar->bulan_dibayar . '-' . $bayar->tahun_dibayar;
            })->toArray();

        $hasPembayaranUkt = PembayaranUkt::where('id_mahasiswa', $this->id)->exists();

        // 5. LOGIKA UTAMA: Loop Kalender vs Hari Ini
        $listNunggak = [];
        $sekarang = now();

        foreach ($kalenderSPP as $item) {
            $tanggalCek = \Carbon\Carbon::createFromDate($item['tahun'], $item['bulan_angka'], 1);

            if ($tanggalCek->startOfMonth() <= $sekarang->startOfMonth()) {
                $kunciCek = $item['nama'] . '-' . $item['tahun'];
                $isPaidSPP = in_array($kunciCek, $pembayaranDB);

                if (!$isPaidSPP && !$hasPembayaranUkt) {
                    $listNunggak[] = $item['nama'];
                }
            }
        }

        $totalNunggak = count($listNunggak) * $spp->nominal;

        return [
            'total_bulan' => count($listNunggak),
            'total_rupiah' => $totalNunggak,
            'list_bulan' => $listNunggak,
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id_siswa');
    }

    public function pembayarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_siswa');
    }

    public function nilais(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Nilai::class, 'id_siswa');
    }

    public function presensis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Presensi::class, 'id_siswa');
    }

    public function krs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Krs::class, 'id_siswa');
    }

    public function submissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Submission::class, 'id_siswa');
    }

    public function earlyWarningLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EarlyWarningLog::class, 'id_siswa');
    }

    public function skpiAktivitas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SkpiAktivitas::class, 'id_siswa');
    }
}