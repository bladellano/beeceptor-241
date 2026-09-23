<?php

namespace Database\Factories;

use App\Enums\PathMatchType;
use App\Models\Endpoint;
use App\Models\MockRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MockRule>
 */
class MockRuleFactory extends Factory
{
    protected $model = MockRule::class;

    public function definition(): array
    {
        return [
            'endpoint_id' => Endpoint::factory(),
            'name' => fake()->words(3, true),
            'priority' => 1,
            'method' => 'GET',
            'path_pattern' => '/users',
            'path_match_type' => PathMatchType::Exact,
            'query_conditions' => [],
            'header_conditions' => [],
            'body_conditions' => [],
            'response_status' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
            'response_body' => '{"ok":true}',
            'response_delay' => 0,
            'is_active' => true,
        ];
    }
}
