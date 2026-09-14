<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class OrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->attributes->get('admin_profile')['role'] === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/'],
            'plan' => ['required', Rule::in(['free', 'starter', 'pro', 'enterprise'])],
            'request_id' => ['required', 'uuid'],
            'expected_updated_at' => [$this->isMethod('PATCH') ? 'required' : 'nullable', 'date'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
            'confirmed' => ['accepted'],
        ];
    }
}
