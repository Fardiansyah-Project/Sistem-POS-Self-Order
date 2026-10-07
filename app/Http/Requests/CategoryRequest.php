<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name'       => 'required|string|max:100|unique:categories,name' . ($categoryId ? ',' . $categoryId : ''),
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'is_active'  => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string'   => 'The category name must be a string.',
            'name.max'      => 'The category name may not be greater than 100 characters.',
            'name.unique'   => 'The category name has already been taken.',
            'icon.string'   => 'The icon must be a string.',
            'icon.max'      => 'The icon may not be greater than 50 characters.',
            'sort_order.required' => 'The sort order is required.',
            'sort_order.integer'  => 'The sort order must be an integer.',
            'sort_order.min'      => 'The sort order must be at least 0.',
            'is_active.required'  => 'The active status is required.',
            'is_active.boolean'   => 'The active status must be true or false.',
        ];
    }
}
