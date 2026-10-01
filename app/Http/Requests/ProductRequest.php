<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order'   => 'required|integer|min:0',
            'is_available' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama produk harus diisi.',
            'name.unique'          => 'Nama produk sudah terdaftar.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'price.required'       => 'Harga harus diisi.',
            'price.min'            => 'Harga minimal Rp 0.',
            'sort_order.required'  => 'Urutan tampil harus diisi.',
            'is_available.required' => 'Status ketersediaan harus diisi.',
        ];
    }
}
