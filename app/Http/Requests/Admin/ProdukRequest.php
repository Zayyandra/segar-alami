<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama'        => ['required', 'string', 'max:150'],
            'deskripsi'   => ['nullable', 'string', 'max:1000'],
            'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'kategori_id' => 'kategori',
            'nama'        => 'nama produk',
            'deskripsi'   => 'deskripsi',
            'foto'        => 'foto produk',
            'is_active'   => 'status aktif',
        ];
    }
}
