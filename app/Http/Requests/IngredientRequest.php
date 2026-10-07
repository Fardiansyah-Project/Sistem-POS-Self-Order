<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
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
        $ingredientId = $this->route('ingredient')?->id;

        return [
            'name' => ['required', 'string', 'max:100', 'unique:ingredients,name' . ($ingredientId ? ',' . $ingredientId : '')],
            'unit' => ['required', 'string', 'max:20'],
            'stock_quantity' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.unique' => 'Nama bahan baku sudah terdaftar.',
            'name.max' => 'Nama bahan baku maksimal 100 karakter.',
            'unit.required' => 'Satuan bahan baku wajib diisi.',
            'unit.max' => 'Satuan maksimal 20 karakter.',
            'stock_quantity.required' => 'Stok saat ini wajib diisi.',
            'stock_quantity.numeric' => 'Stok harus berupa angka.',
            'stock_quantity.min' => 'Stok tidak boleh kurang dari nol.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
            'minimum_stock.numeric' => 'Batas stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Batas stok minimum tidak boleh kurang dari nol.',
        ];
    }
}
