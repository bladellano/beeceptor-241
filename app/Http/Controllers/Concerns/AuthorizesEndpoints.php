<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Endpoint;
use App\Models\MockRule;
use Symfony\Component\HttpFoundation\Response;

trait AuthorizesEndpoints
{
    protected function authorizeEndpoint(Endpoint $endpoint): ?Response
    {
        if ($endpoint->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return null;
    }

    protected function authorizeRule(MockRule $rule): ?Response
    {
        $rule->loadMissing('endpoint');

        return $this->authorizeEndpoint($rule->endpoint);
    }

    protected function ensureOwnsEndpoint(Endpoint $endpoint): void
    {
        abort_if($endpoint->user_id !== auth()->id(), 403);
    }
}
