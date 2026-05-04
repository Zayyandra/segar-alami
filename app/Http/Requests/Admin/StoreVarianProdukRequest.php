<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVarianProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'produk_id'   => ['required', 'exists:produk,id'],
            'nama_varian' => ['required', 'string', 'max:100'],
            'ukuran'      => ['nullable', 'string', 'max:50'],
            'harga'       => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function attributes(): array
    {
        return [
            'produk_id'   => 'produk',
            'nama_varian' => 'nama varian',
        ];
    }
}
