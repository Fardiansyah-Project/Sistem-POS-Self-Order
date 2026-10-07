<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PosRequest extends FormRequest
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
            'customer_name' => ['required', 'string', 'max:150'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'json'],
            'payment_type' => ['required', 'string', 'in:cash,qris,gopay,bank_transfer'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.max' => 'Nama pelanggan maksimal 150 karakter.',
            'table_number.max' => 'Nomor meja maksimal 50 karakter.',
            'items.required' => 'Keranjang pesanan wajib diisi.',
            'items.json' => 'Format item pesanan tidak valid.',
            'payment_type.in' => 'Metode pembayaran yang dipilih tidak valid.',
            'amount_paid.required' => 'Nominal pembayaran wajib diisi.',
            'amount_paid.numeric' => 'Nominal pembayaran harus berupa angka.',
            'amount_paid.min' => 'Nominal pembayaran tidak boleh kurang dari nol.',
        ];
    }
}
