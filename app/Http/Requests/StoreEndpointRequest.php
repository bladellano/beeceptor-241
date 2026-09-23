<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ParsesJsonFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEndpointRequest extends FormRequest
{
    use ParsesJsonFields;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->decodeJsonFields(['fallback_headers']);
    }

    public function rules(): array
    {
        $reserved = config('mock.reserved_slugs', []);

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:endpoints,slug',
                Rule::notIn($reserved),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'fallback_status' => ['sometimes', 'integer', 'min:100', 'max:599'],
            'fallback_headers' => ['sometimes', 'nullable', 'array'],
            'fallback_body' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
