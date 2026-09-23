<?php

namespace Tests\Feature;

use App\Models\Endpoint;
use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PruneRequestLogsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_prune_removes_old_logs(): void
    {
        config(['mock.request_log_retention_days' => 7]);

        $endpoint = Endpoint::factory()->for(User::factory())->create();

        RequestLog::query()->create([
            'endpoint_id' => $endpoint->id,
            'method' => 'GET',
            'url' => 'http://localhost/test',
            'path' => '/test',
            'fallback_used' => true,
            'response_status' => 200,
            'duration_ms' => 1,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        RequestLog::query()->create([
            'endpoint_id' => $endpoint->id,
            'method' => 'GET',
            'url' => 'http://localhost/new',
            'path' => '/new',
            'fallback_used' => true,
            'response_status' => 200,
            'duration_ms' => 1,
            'created_at' => Carbon::now()->subDay(),
        ]);

        $this->artisan('requests:prune')->assertSuccessful();

        $this->assertDatabaseCount('request_logs', 1);
    }
}
