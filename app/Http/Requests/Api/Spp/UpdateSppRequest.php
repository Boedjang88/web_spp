<?php

namespace App\Http\Requests\Api\Spp;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateSppRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $sppId = $this->route('spp') ?? $this->route('id');

        return [
            'tahun' => [
                'sometimes',
                'required',
                'integer',
                'digits:4',
                Rule::unique('spps', 'tahun')->ignore($sppId),
            ],
            'nominal' => 'sometimes|required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun SPP wajib diisi.',
            'tahun.digits' => 'Tahun SPP harus 4 digit angka.',
            'tahun.unique' => 'Tahun SPP sudah digunakan.',
            'nominal.required' => 'Nominal SPP wajib diisi.',
            'nominal.numeric' => 'Nominal SPP harus berupa angka.',
            'nominal.min' => 'Nominal SPP minimal 0.',
        ];
    }
}
