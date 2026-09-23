<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEndpointRequest;
use App\Http\Requests\UpdateEndpointRequest;
use App\Models\Endpoint;
use Illuminate\Http\JsonResponse;

class EndpointController extends Controller
{
    use AuthorizesEndpoints;

    public function index(): JsonResponse
    {
        $endpoints = Endpoint::query()
            ->where('user_id', auth()->id())
            ->withCount(['mockRules', 'requestLogs'])
            ->latest()
            ->get();

        return response()->json($endpoints);
    }

    public function store(StoreEndpointRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['fallback_status'] ??= config('mock.fallback_status');
        $data['fallback_headers'] ??= config('mock.fallback_headers');
        $data['fallback_body'] ??= config('mock.fallback_body');
        $data['is_active'] ??= true;

        $endpoint = Endpoint::query()->create($data);

        return response()->json($endpoint, 201);
    }

    public function show(Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        $endpoint->loadCount(['mockRules', 'requestLogs']);

        return response()->json($endpoint);
    }

    public function update(UpdateEndpointRequest $request, Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        $endpoint->update($request->validated());

        return response()->json($endpoint);
    }

    public function destroy(Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        $endpoint->delete();

        return response()->json(null, 204);
    }
}
