<?php

namespace App\Http\Requests;

class UpdateMockRuleRequest extends StoreMockRuleRequest
{
    public function rules(): array
    {
        $rules = $this->ruleRules();
        foreach (array_keys($rules) as $key) {
            if (! str_contains($key, '.')) {
                $rules[$key] = array_merge(['sometimes'], (array) $rules[$key]);
            }
        }

        return $rules;
    }
}
