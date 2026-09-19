<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'uuid',
            ],

            'company_id' => [
                'nullable',
                'uuid',
            ],

            'title' => [
                'required',
                'string',
                'min:2',
                'max:200',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
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
            'category_id.required' =>
                'Please select a job category.',

            'category_id.uuid' =>
                'The selected job category is invalid.',

            'company_id.uuid' =>
                'The selected company is invalid.',

            'title.required' =>
                'Job title is required.',

            'title.min' =>
                'Job title must contain at least 2 characters.',

            'description.max' =>
                'Job description may not exceed 5000 characters.',

            'is_active.required' =>
                'Please select whether this job is enabled.',
        ];
    }
}
