<?php

namespace App\Http\Requests\Api\Kelas;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $kelasId = $this->route('kela') ?? $this->route('id') ?? $this->route('kela_id');

        return [
            'nama_kelas' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($kelasId),
            ],
            'kompetensi_keahlian' => 'sometimes|required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan kelas lain.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian wajib diisi.',
        ];
    }
}
