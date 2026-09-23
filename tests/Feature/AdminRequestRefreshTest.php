<?php

namespace Tests\Feature;

use App\Models\Endpoint;
use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRequestRefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_requests_index_supports_ajax_partial(): void
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->for($user)->create(['slug' => 'refresh-test']);

        RequestLog::query()->create([
            'endpoint_id' => $endpoint->id,
            'method' => 'GET',
            'url' => 'http://localhost/refresh-test/hook',
            'path' => '/hook',
            'fallback_used' => true,
            'response_status' => 200,
            'duration_ms' => 3,
            'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/endpoints/'.$endpoint->id.'/requests', [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->assertSee('/hook', false)
            ->assertDontSee('<!DOCTYPE html>', false);
    }
}
