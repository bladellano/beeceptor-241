<?php

namespace App\Services;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockRequestService
{
    public function __construct(
        private MockRuleMatcher $matcher,
        private MockResponseService $responseService,
        private RequestLogService $requestLogService,
    ) {}

    public function handle(Request $request, string $slug, ?string $subPath): Response
    {
        if (in_array($slug, config('mock.reserved_slugs', []), true)) {
            return $this->responseService->notFound();
        }

        $endpoint = Endpoint::query()->where('slug', $slug)->first();

        if ($endpoint === null) {
            return $this->responseService->notFound();
        }

        if (! $endpoint->is_active) {
            return $this->responseService->inactive();
        }

        $path = $this->normalizeSubPath($subPath);
        $started = hrtime(true);

        $rules = $endpoint->mockRules()
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $matchedRule = $this->matcher->findFirstMatch($rules, $request, $path);

        if ($matchedRule !== null) {
            $payload = $this->responseService->fromRule($matchedRule);
            $fallbackUsed = false;
        } else {
            $payload = $this->responseService->fallback($endpoint);
            $fallbackUsed = true;
        }

        if ($payload['delay_ms'] > 0) {
            usleep($payload['delay_ms'] * 1000);
        }

        $durationMs = (int) ((hrtime(true) - $started) / 1_000_000);

        $this->requestLogService->log(
            $endpoint,
            $request,
            $path,
            $payload,
            $matchedRule,
            $fallbackUsed,
            $durationMs,
        );

        return $this->applyCors($request, $this->responseService->buildHttpResponse($payload));
    }

    private function normalizeSubPath(?string $subPath): string
    {
        if ($subPath === null || $subPath === '') {
            return '/';
        }

        return str_starts_with($subPath, '/') ? $subPath : '/'.$subPath;
    }

    private function applyCors(Request $request, Response $response): Response
    {
        $origin = config('mock.cors_allowed_origins', '*');
        $methods = config('mock.cors_allowed_methods', '*');
        $headers = config('mock.cors_allowed_headers', '*');

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', $methods);
        $response->headers->set('Access-Control-Allow-Headers', $headers);

        if (strtoupper($request->method()) === 'OPTIONS') {
            return response('', 204, $response->headers->all());
        }

        return $response;
    }
}
