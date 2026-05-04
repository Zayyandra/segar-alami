<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $id = $this->route('kategori')?->id;

        return [
            'nama' => [
                'required', 'string', 'max:100',
                Rule::unique('kategori', 'nama')->ignore($id),
            ],
            'urutan'    => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
            'nama'      => 'nama kategori',
            'urutan'    => 'urutan tampil',
            'is_active' => 'status aktif',
        ];
    }
}
