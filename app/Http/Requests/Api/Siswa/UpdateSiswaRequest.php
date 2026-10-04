<?php

namespace App\Http\Requests\Api\Siswa;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $siswaId = $this->route('siswa') ?? $this->route('id');

        return [
            'nisn' => [
                'sometimes',
                'required',
                'string',
                'size:10',
                Rule::unique('siswas', 'nisn')->ignore($siswaId),
            ],
            'nis' => 'sometimes|required|string|max:8',
            'nama' => 'sometimes|required|string|max:50',
            'id_kelas' => 'sometimes|required|exists:kelas,id',
            'alamat' => 'sometimes|required|string',
            'no_telp' => 'sometimes|required|string|max:15',
            'id_spp' => 'sometimes|required|exists:spps,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.size' => 'NISN harus 10 digit.',
            'nisn.unique' => 'NISN sudah digunakan siswa lain.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'id_kelas.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'id_spp.exists' => 'Tarif SPP yang dipilih tidak ditemukan.',
        ];
    }
}
