<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nisn' => $this->nisn,
            'nis' => $this->nis,
            'nama' => $this->nama,
            'id_kelas' => $this->id_kelas,
            'kelas' => new KelasResource($this->whenLoaded('kelas')),
            'alamat' => $this->alamat,
            'no_telp' => $this->no_telp,
            'id_spp' => $this->id_spp,
            'spp' => new SppResource($this->whenLoaded('spp')),
            'info_tunggakan' => $this->when($this->relationLoaded('spp') || $this->id_spp, function () {
                return $this->info_tunggakan;
            }),
            'pembayarans' => PembayaranResource::collection($this->whenLoaded('pembayarans')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
