<?php

namespace App\Http\Middleware;

use App\Services\MockResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class MockRateLimit
{
    public function __construct(private MockResponseService $responseService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'mock:'.$request->ip();
        $maxAttempts = config('mock.rate_limit', 60);
        $decayMinutes = config('mock.rate_limit_window', 1);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return $this->responseService->rateLimited();
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
