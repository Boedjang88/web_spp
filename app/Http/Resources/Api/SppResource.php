<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SppResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tahun' => (int) $this->tahun,
            'nominal' => (int) $this->nominal,
            'formatted_nominal' => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
            'total_siswa' => $this->whenCounted('siswas', $this->siswas_count),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
