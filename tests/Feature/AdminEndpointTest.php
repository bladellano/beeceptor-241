<?php

namespace Tests\Feature;

use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_area_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_can_create_and_manage_endpoints(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/endpoints', [
                'name' => 'Payment Test',
                'slug' => 'payment-test',
                'description' => 'Demo',
                'is_active' => '1',
                'fallback_status' => 200,
                'fallback_body' => 'fallback',
                'fallback_headers' => '{"Content-Type":"text/plain"}',
            ])
            ->assertRedirect();

        $endpoint = Endpoint::query()->where('slug', 'payment-test')->first();
        $this->assertNotNull($endpoint);
        $this->assertSame($user->id, $endpoint->user_id);

        $this->actingAs($user)
            ->patch("/admin/endpoints/{$endpoint->id}/toggle")
            ->assertRedirect();

        $endpoint->refresh();
        $this->assertFalse($endpoint->is_active);

        $other = User::factory()->create();
        $this->actingAs($other)
            ->get("/admin/endpoints/{$endpoint->id}/edit")
            ->assertForbidden();
    }

    public function test_slug_must_be_unique(): void
    {
        $user = User::factory()->create();
        Endpoint::factory()->for($user)->create(['slug' => 'dupe']);

        $this->actingAs($user)
            ->from('/admin/endpoints/create')
            ->post('/admin/endpoints', [
                'name' => 'Other',
                'slug' => 'dupe',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_admin_api_requires_auth(): void
    {
        $this->getJson('/api/endpoints')->assertUnauthorized();
    }

    public function test_admin_api_crud(): void
    {
        $user = User::factory()->create();

        $create = $this->actingAs($user)->postJson('/api/endpoints', [
            'name' => 'API Endpoint',
            'slug' => 'api-endpoint',
        ])->assertCreated();

        $id = $create->json('id');

        $this->actingAs($user)->getJson('/api/endpoints/'.$id)->assertOk();
        $this->actingAs($user)->putJson('/api/endpoints/'.$id, ['name' => 'Updated'])->assertOk();
        $this->actingAs($user)->deleteJson('/api/endpoints/'.$id)->assertNoContent();
    }
}
