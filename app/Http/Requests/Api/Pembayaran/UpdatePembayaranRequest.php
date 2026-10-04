<?php

namespace App\Http\Requests\Api\Pembayaran;

use App\Http\Requests\Api\BaseApiRequest;

class UpdatePembayaranRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'id_siswa' => 'sometimes|required|exists:siswas,id',
            'tgl_bayar' => 'sometimes|required|date',
            'bulan_dibayar' => 'sometimes|required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun_dibayar' => 'sometimes|required|integer|digits:4',
            'id_spp' => 'sometimes|required|exists:spps,id',
            'jumlah_bayar' => 'sometimes|required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_siswa.exists' => 'Data siswa tidak ditemukan.',
            'bulan_dibayar.in' => 'Nama bulan tidak valid.',
            'id_spp.exists' => 'Data SPP tidak ditemukan.',
        ];
    }
}
