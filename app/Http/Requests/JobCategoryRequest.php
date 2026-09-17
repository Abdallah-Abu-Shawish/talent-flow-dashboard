<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class JobCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:160',
            ],

            'description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'expected_updated_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Category name is required.',

            'name.min' =>
                'Category name must contain at least 2 characters.',

            'name.max' =>
                'Category name may not exceed 160 characters.',

            'description.max' =>
                'Category description may not exceed 3000 characters.',

            'is_active.required' =>
                'Please select whether this category is enabled.',
        ];
    }
}