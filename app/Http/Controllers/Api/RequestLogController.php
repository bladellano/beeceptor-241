<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Models\Endpoint;
use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;

class RequestLogController extends Controller
{
    use AuthorizesEndpoints;

    public function index(Endpoint $endpoint): JsonResponse
    {
        if ($response = $this->authorizeEndpoint($endpoint)) {
            return $response;
        }

        $logs = $endpoint->requestLogs()
            ->with('matchedRule')
            ->latest('created_at')
            ->paginate(30);

        return response()->json($logs);
    }

    public function show(RequestLog $requestLog): JsonResponse
    {
        $requestLog->load(['endpoint', 'matchedRule']);

        if ($response = $this->authorizeEndpoint($requestLog->endpoint)) {
            return $response;
        }

        return response()->json($requestLog);
    }
}
