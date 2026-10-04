<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_petugas' => $this->id_petugas,
            'petugas' => new UserResource($this->whenLoaded('petugas')),
            'id_siswa' => $this->id_siswa,
            'siswa' => new SiswaResource($this->whenLoaded('siswa')),
            'tgl_bayar' => $this->tgl_bayar,
            'bulan_dibayar' => $this->bulan_dibayar,
            'tahun_dibayar' => $this->tahun_dibayar,
            'periode_spp' => $this->bulan_dibayar . ' ' . $this->tahun_dibayar,
            'id_spp' => $this->id_spp,
            'spp' => new SppResource($this->whenLoaded('spp')),
            'jumlah_bayar' => (int) $this->jumlah_bayar,
            'formatted_jumlah_bayar' => 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
