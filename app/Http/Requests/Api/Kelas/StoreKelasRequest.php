<?php

namespace App\Http\Requests\Api\Kelas;

use App\Http\Requests\Api\BaseApiRequest;

class StoreKelasRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'kompetensi_keahlian' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah ada.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian wajib diisi.',
        ];
    }
}
