<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'nama'             => ['required', 'string', 'max:100', Rule::unique('bahan_baku', 'nama')->ignore($this->route('bahanBaku'))],
            'satuan'           => ['required', 'string', 'max:20'],
            'kategori_bb'      => ['required', Rule::in(['utama', 'pendukung'])],
            'stok_minimum'     => ['nullable', 'numeric', 'min:0'],
            'harga_per_satuan' => ['nullable', 'numeric', 'min:0'],
            'tracking_ss_rop'  => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tracking_ss_rop' => $this->boolean('tracking_ss_rop'),
            'is_active'       => $this->boolean('is_active'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'nama'        => 'nama bahan baku',
            'kategori_bb' => 'kategori',
        ];
    }
}
