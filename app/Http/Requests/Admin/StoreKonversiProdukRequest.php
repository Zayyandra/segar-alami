<?php
// app/Http/Requests/Admin/StoreKonversiProdukRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKonversiProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'varian_produk_id'  => ['required', 'integer', 'exists:varian_produk,id'],
            'bahan_baku_id'     => [
                'required',
                'integer',
                'exists:bahan_baku,id',
                Rule::unique('konversi_produk', 'bahan_baku_id')
                    ->where(fn ($query) => $query->where('varian_produk_id', $this->integer('varian_produk_id'))),
            ],
            'jumlah_per_satuan' => ['required', 'numeric', 'min:0.0001'],
        ];
    }

    public function attributes(): array
    {
        return [
            'varian_produk_id'  => 'varian produk',
            'bahan_baku_id'     => 'bahan baku',
            'jumlah_per_satuan' => 'jumlah per satuan',
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_baku_id.unique' => 'Kombinasi varian produk dan bahan baku ini sudah terdaftar.',
        ];
    }
}
