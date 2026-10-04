<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_siswa',
        'id_mapel',
        'id_guru',
        'semester',
        'tahun_ajaran',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'predikat',
        'catatan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    /**
     * Hitung nilai akhir & tentukan predikat
     * Bobot: 30% Tugas + 30% UTS + 40% UAS
     */
    public static function kalkulasiNilai(float $tugas, float $uts, float $uas): array
    {
        $akhir = round(($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4), 2);

        if ($akhir >= 90) {
            $predikat = 'A';
        } elseif ($akhir >= 80) {
            $predikat = 'B';
        } elseif ($akhir >= 70) {
            $predikat = 'C';
        } else {
            $predikat = 'D';
        }

        return [
            'nilai_akhir' => $akhir,
            'predikat' => $predikat,
        ];
    }
}
