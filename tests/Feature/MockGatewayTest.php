<?php

namespace Tests\Feature;

use App\Enums\ConditionOperator;
use App\Enums\PathMatchType;
use App\Models\Endpoint;
use App\Models\MockRule;
use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class MockGatewayTest extends TestCase
{
    use RefreshDatabase;

    private function createEndpoint(array $attributes = []): Endpoint
    {
        $user = User::factory()->create();

        return Endpoint::factory()->for($user)->create($attributes);
    }

    public function test_unknown_endpoint_returns_404_json(): void
    {
        $this->getJson('/does-not-exist/users')
            ->assertNotFound()
            ->assertJson(['message' => 'Mock endpoint not found.']);
    }

    public function test_inactive_endpoint_returns_410(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'disabled-api', 'is_active' => false]);

        $this->getJson('/'.$endpoint->slug.'/users')
            ->assertStatus(410)
            ->assertJson(['message' => 'Mock endpoint is disabled.']);
    }

    public function test_post_without_csrf_token_reaches_the_mock(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'ft241']);

        $this->post('/ft241/api/v2/integration/login', ['user_id' => 'trk_10'], [
            'Accept' => 'application/json',
        ])->assertOk();

        $this->assertDatabaseHas('request_logs', [
            'endpoint_id' => $endpoint->id,
            'method' => 'POST',
            'path' => '/api/v2/integration/login',
        ]);
    }

    public function test_fallback_response_when_no_rule_matches(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'payment-test']);

        $this->get('/payment-test/users')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Hey ya! Great to see you here');

        $this->assertDatabaseHas('request_logs', [
            'endpoint_id' => $endpoint->id,
            'method' => 'GET',
            'path' => '/users',
            'fallback_used' => true,
        ]);
    }

    public function test_rule_match_returns_configured_response(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'payment-test']);

        MockRule::factory()->for($endpoint)->create([
            'method' => 'POST',
            'path_pattern' => '/users',
            'path_match_type' => PathMatchType::Exact,
            'response_status' => 201,
            'response_headers' => ['Content-Type' => 'application/json'],
            'response_body' => '{"id":1,"name":"John"}',
        ]);

        $this->postJson('/payment-test/users', ['name' => 'John'])
            ->assertCreated()
            ->assertJson(['id' => 1, 'name' => 'John']);
    }

    public function test_lower_priority_rule_wins(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'prio-test']);

        MockRule::factory()->for($endpoint)->create([
            'priority' => 2,
            'name' => 'second',
            'response_body' => 'second',
        ]);

        MockRule::factory()->for($endpoint)->create([
            'priority' => 1,
            'name' => 'first',
            'response_body' => 'first',
        ]);

        $this->get('/prio-test/users')->assertSee('first');
    }

    public function test_path_starts_with_matching(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'path-test']);

        MockRule::factory()->for($endpoint)->create([
            'path_pattern' => '/users',
            'path_match_type' => PathMatchType::StartsWith,
            'response_body' => 'matched',
        ]);

        $this->get('/path-test/users/123/profile')->assertSee('matched');
    }

    public function test_query_header_and_body_conditions(): void
    {
        $endpoint = $this->createEndpoint(['slug' => 'cond-test']);

        MockRule::factory()->for($endpoint)->create([
            'method' => 'POST',
            'path_pattern' => '/users',
            'query_conditions' => [
                ['field' => 'status', 'operator' => ConditionOperator::Equals->value, 'value' => 'active'],
            ],
            'header_conditions' => [
                ['field' => 'X-Test', 'operator' => ConditionOperator::Equals->value, 'value' => 'true'],
            ],
            'body_conditions' => [
                ['field' => 'email', 'operator' => ConditionOperator::Equals->value, 'value' => 'john@example.com'],
            ],
            'response_body' => 'all-matched',
        ]);

        $this->withHeaders(['X-Test' => 'true'])
            ->postJson('/cond-test/users?status=active', ['email' => 'john@example.com'])
            ->assertSee('all-matched');
    }

    public function test_rate_limit_returns_429(): void
    {
        config(['mock.rate_limit' => 2, 'mock.rate_limit_window' => 1]);
        RateLimiter::clear('mock:127.0.0.1');

        $this->createEndpoint(['slug' => 'rate-test']);

        $this->get('/rate-test/a')->assertOk();
        $this->get('/rate-test/b')->assertOk();
        $this->get('/rate-test/c')->assertStatus(429);
    }

    public function test_request_body_size_limit(): void
    {
        config(['mock.max_request_body_size' => 10]);
        $this->createEndpoint(['slug' => 'body-test']);

        $this->call('POST', '/body-test/hook', [], [], [], [
            'CONTENT_LENGTH' => '99999',
            'CONTENT_TYPE' => 'application/json',
        ], '{"a":1}')->assertStatus(413);
    }
}
