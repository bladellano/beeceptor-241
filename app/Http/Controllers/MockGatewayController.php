<?php

namespace App\Http\Controllers;

use App\Services\MockRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockGatewayController extends Controller
{
    public function __construct(private MockRequestService $mockRequestService) {}

    public function handle(Request $request, string $slug, ?string $path = null): Response
    {
        return $this->mockRequestService->handle($request, $slug, $path);
    }
}
