<?php

namespace App\Services;

use App\Enums\ConditionOperator;
use App\Enums\PathMatchType;
use App\Models\MockRule;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MockRuleMatcher
{
    /**
     * @param  Collection<int, MockRule>  $rules
     */
    public function findFirstMatch(Collection $rules, Request $request, string $path): ?MockRule
    {
        foreach ($rules as $rule) {
            if ($this->matches($rule, $request, $path)) {
                return $rule;
            }
        }

        return null;
    }

    public function matches(MockRule $rule, Request $request, string $path): bool
    {
        if (strtoupper($rule->method) !== strtoupper($request->method())) {
            return false;
        }

        if (! $this->matchesPath($rule, $path)) {
            return false;
        }

        if (! $this->matchesConditions($rule->query_conditions ?? [], $request->query->all())) {
            return false;
        }

        if (! $this->matchesHeaderConditions($rule->header_conditions ?? [], $request)) {
            return false;
        }

        if (! $this->matchesBodyConditions($rule->body_conditions ?? [], $request)) {
            return false;
        }

        return true;
    }

    private function matchesPath(MockRule $rule, string $path): bool
    {
        $pattern = $rule->path_pattern;
        if ($pattern !== '' && ! str_starts_with($pattern, '/')) {
            $pattern = '/'.$pattern;
        }

        $normalizedPath = $path === '' ? '/' : (str_starts_with($path, '/') ? $path : '/'.$path);

        return match ($rule->path_match_type) {
            PathMatchType::Exact => $normalizedPath === $pattern,
            PathMatchType::StartsWith => $normalizedPath === $pattern
                || str_starts_with($normalizedPath, rtrim($pattern, '/').'/')
                || ($normalizedPath === rtrim($pattern, '/')),
            PathMatchType::Contains => str_contains($normalizedPath, trim($pattern, '/')),
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     * @param  array<string, mixed>  $values
     */
    private function matchesConditions(array $conditions, array $values): bool
    {
        foreach ($conditions as $condition) {
            $field = (string) ($condition['field'] ?? '');
            $operator = ConditionOperator::tryFrom((string) ($condition['operator'] ?? ''));
            $expected = $condition['value'] ?? null;

            if ($operator === null || $field === '') {
                return false;
            }

            $actual = $values[$field] ?? null;

            if (! $this->evaluateOperator($operator, $actual, $expected)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     */
    private function matchesHeaderConditions(array $conditions, Request $request): bool
    {
        foreach ($conditions as $condition) {
            $field = (string) ($condition['field'] ?? '');
            $operator = ConditionOperator::tryFrom((string) ($condition['operator'] ?? ''));
            $expected = $condition['value'] ?? null;

            if ($operator === null || $field === '') {
                return false;
            }

            $actual = $request->header($field);

            if (! $this->evaluateOperator($operator, $actual, $expected)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     */
    private function matchesBodyConditions(array $conditions, Request $request): bool
    {
        if ($conditions === []) {
            return true;
        }

        $payload = $request->json()->all();
        if (! is_array($payload)) {
            return false;
        }

        return $this->matchesConditions($conditions, $payload);
    }

    private function evaluateOperator(ConditionOperator $operator, mixed $actual, mixed $expected): bool
    {
        return match ($operator) {
            ConditionOperator::Exists => $actual !== null && $actual !== '',
            ConditionOperator::Equals => (string) $actual === (string) $expected,
            ConditionOperator::NotEquals => (string) $actual !== (string) $expected,
        };
    }
}
