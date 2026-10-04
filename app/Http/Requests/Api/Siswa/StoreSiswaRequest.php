<?php

namespace App\Http\Requests\Api\Siswa;

use App\Http\Requests\Api\BaseApiRequest;

class StoreSiswaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nisn' => 'required|string|size:10|unique:siswas,nisn',
            'nis' => 'required|string|max:8',
            'nama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'id_spp' => 'required|exists:spps,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.size' => 'NISN harus terdiri dari 10 karakter.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nis.required' => 'NIS wajib diisi.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas yang dipilih tidak valid.',
            'alamat.required' => 'Alamat siswa wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'id_spp.required' => 'Tarif SPP wajib dipilih.',
            'id_spp.exists' => 'Tarif SPP yang dipilih tidak valid.',
        ];
    }
}
