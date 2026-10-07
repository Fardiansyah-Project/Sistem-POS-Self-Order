<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForecastRequest extends FormRequest
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
            'weights' => ['required', 'string', 'regex:/^\\s*\\d+(?:\\s*,\\s*\\d+)*\\s*$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'weights.required' => 'Masukkan bobot periode WMA.',
            'weights.regex' => 'Bobot harus berupa angka positif yang dipisahkan koma, contoh: 1,2,3.',
        ];
    }
}
