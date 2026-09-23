<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMockRuleRequest;
use App\Http\Requests\UpdateMockRuleRequest;
use App\Models\Endpoint;
use App\Models\MockRule;
use Illuminate\Http\JsonResponse;

class MockRuleController extends Controller
{
    use AuthorizesEndpoints;

    public function index(Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        return response()->json($endpoint->mockRules()->orderBy('priority')->get());
    }

    public function store(StoreMockRuleRequest $request, Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        $rule = $endpoint->mockRules()->create($request->validated());

        return response()->json($rule, 201);
    }

    public function update(UpdateMockRuleRequest $request, MockRule $rule): JsonResponse
    {
        if ($response = $this->authorizeRule($rule)) {
            return $response;
        }

        $rule->update($request->validated());

        return response()->json($rule);
    }

    public function destroy(MockRule $rule): JsonResponse
    {
        if ($response = $this->authorizeRule($rule)) {
            return $response;
        }

        $rule->delete();

        return response()->json(null, 204);
    }
}
