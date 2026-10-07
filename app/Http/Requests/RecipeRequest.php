<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
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
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'quantity_needed' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'ingredient_id.required' => 'Pilih bahan baku terlebih dahulu.',
            'ingredient_id.exists' => 'Bahan baku yang dipilih tidak ditemukan.',
            'quantity_needed.required' => 'Takaran bahan wajib diisi.',
            'quantity_needed.numeric' => 'Takaran harus berupa angka.',
            'quantity_needed.min' => 'Takaran minimal 0,01.',
        ];
    }
}
