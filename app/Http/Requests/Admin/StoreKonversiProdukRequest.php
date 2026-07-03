<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKonversiProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'varian_produk_id'                => ['required', 'integer', 'exists:varian_produk,id'],
            'items'                           => ['required', 'array', 'min:1'],
            'items.*.bahan_baku_id'           => ['required', 'integer', 'exists:bahan_baku,id'],
            'items.*.jumlah_per_satuan'       => ['required', 'numeric', 'min:0.0001'],
        ];
    }

    public function attributes(): array
    {
        return [
            'varian_produk_id'          => 'varian produk',
            'items.*.bahan_baku_id'     => 'bahan baku',
            'items.*.jumlah_per_satuan' => 'jumlah per satuan',
        ];
    }
}
