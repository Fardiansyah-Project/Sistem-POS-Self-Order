<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class KitchenRequest extends FormRequest
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
            'order_status' => ['required', 'in:processing,ready,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_status.required' => 'Status pesanan wajib dipilih.',
            'order_status.in' => 'Status pesanan yang dipilih tidak valid.',
        ];
    }
}
