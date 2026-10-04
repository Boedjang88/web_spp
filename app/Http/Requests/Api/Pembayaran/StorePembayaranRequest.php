<?php

namespace App\Http\Requests\Api\Pembayaran;

use App\Http\Requests\Api\BaseApiRequest;

class StorePembayaranRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'id_siswa' => 'required|exists:siswas,id',
            'tgl_bayar' => 'nullable|date',
            'bulan_dibayar' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun_dibayar' => 'required|integer|digits:4',
            'id_spp' => 'nullable|exists:spps,id',
            'jumlah_bayar' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_siswa.required' => 'Siswa wajib dipilih.',
            'id_siswa.exists' => 'Data siswa tidak ditemukan.',
            'tgl_bayar.date' => 'Format tanggal bayar harus valid (YYYY-MM-DD).',
            'bulan_dibayar.required' => 'Bulan yang dibayar wajib diisi.',
            'bulan_dibayar.in' => 'Nama bulan tidak valid (Gunakan: Januari, Februari, dst).',
            'tahun_dibayar.required' => 'Tahun yang dibayar wajib diisi.',
            'id_spp.exists' => 'Data SPP tidak ditemukan.',
        ];
    }
}
