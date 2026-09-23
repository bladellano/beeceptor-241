<?php

namespace App\Http\Requests;

use App\Enums\ConditionOperator;
use App\Enums\PathMatchType;
use App\Http\Requests\Concerns\ParsesJsonFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMockRuleRequest extends FormRequest
{
    use ParsesJsonFields;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->decodeJsonFields([
            'query_conditions',
            'header_conditions',
            'body_conditions',
            'response_headers',
        ]);
    }

    public function rules(): array
    {
        return $this->ruleRules();
    }

    /**
     * @return array<string, mixed>
     */
    protected function ruleRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:1'],
            'method' => ['required', 'string', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'])],
            'path_pattern' => ['required', 'string', 'max:2048'],
            'path_match_type' => ['required', Rule::enum(PathMatchType::class)],
            'query_conditions' => ['sometimes', 'nullable', 'array'],
            'query_conditions.*.field' => ['required_with:query_conditions', 'string'],
            'query_conditions.*.operator' => ['required_with:query_conditions', Rule::enum(ConditionOperator::class)],
            'header_conditions' => ['sometimes', 'nullable', 'array'],
            'header_conditions.*.field' => ['required_with:header_conditions', 'string'],
            'header_conditions.*.operator' => ['required_with:header_conditions', Rule::enum(ConditionOperator::class)],
            'body_conditions' => ['sometimes', 'nullable', 'array'],
            'body_conditions.*.field' => ['required_with:body_conditions', 'string'],
            'body_conditions.*.operator' => ['required_with:body_conditions', Rule::enum(ConditionOperator::class)],
            'response_status' => ['required', 'integer', 'min:100', 'max:599'],
            'response_headers' => ['sometimes', 'nullable', 'array'],
            'response_body' => ['sometimes', 'nullable', 'string'],
            'response_delay' => ['sometimes', 'integer', 'min:0', 'max:'.config('mock.max_mock_delay_ms')],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
