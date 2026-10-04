<?php

namespace App\Http\Requests\Api\Spp;

use App\Http\Requests\Api\BaseApiRequest;

class StoreSppRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'tahun' => 'required|integer|digits:4|unique:spps,tahun',
            'nominal' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun SPP wajib diisi.',
            'tahun.digits' => 'Tahun SPP harus 4 digit angka (contoh: 2025).',
            'tahun.unique' => 'Tahun SPP sudah ada.',
            'nominal.required' => 'Nominal SPP wajib diisi.',
            'nominal.numeric' => 'Nominal SPP harus berupa angka.',
            'nominal.min' => 'Nominal SPP minimal 0.',
        ];
    }
}
