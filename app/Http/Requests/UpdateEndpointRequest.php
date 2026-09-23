<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ParsesJsonFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEndpointRequest extends FormRequest
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
        $endpoint = $this->route('endpoint');
        $endpointId = is_object($endpoint) ? $endpoint->id : $endpoint;
        $reserved = config('mock.reserved_slugs', []);

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('endpoints', 'slug')->ignore($endpointId),
                Rule::notIn($reserved),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'fallback_status' => ['sometimes', 'integer', 'min:100', 'max:599'],
            'fallback_headers' => ['sometimes', 'nullable', 'array'],
            'fallback_body' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
