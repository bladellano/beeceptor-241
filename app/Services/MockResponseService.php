<?php

namespace App\Services;

use App\Models\Endpoint;
use App\Models\MockRule;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class MockResponseService
{
    public function fromRule(MockRule $rule): array
    {
        $headers = $rule->response_headers ?? [];
        $body = $this->truncate($rule->response_body ?? '', config('mock.max_response_body_size'));
        $delay = min(
            max(0, (int) $rule->response_delay),
            config('mock.max_mock_delay_ms')
        );

        return [
            'status' => (int) $rule->response_status,
            'headers' => $this->normalizeHeaders($headers),
            'body' => $body,
            'delay_ms' => $delay,
        ];
    }

    public function fallback(Endpoint $endpoint): array
    {
        $headers = $endpoint->fallback_headers ?? config('mock.fallback_headers');
        $body = $endpoint->fallback_body ?? config('mock.fallback_body');

        return [
            'status' => (int) ($endpoint->fallback_status ?? config('mock.fallback_status')),
            'headers' => $this->normalizeHeaders($headers),
            'body' => $this->truncate($body, config('mock.max_response_body_size')),
            'delay_ms' => 0,
        ];
    }

    public function buildHttpResponse(array $payload): Response
    {
        return response($payload['body'], $payload['status'], $payload['headers']);
    }

    public function notFound(): BaseResponse
    {
        return response()->json(['message' => 'Mock endpoint not found.'], 404);
    }

    public function inactive(): BaseResponse
    {
        $status = (int) config('mock.inactive_endpoint_status', 410);

        return response()->json(['message' => 'Mock endpoint is disabled.'], $status);
    }

    public function rateLimited(): BaseResponse
    {
        return response()->json(['message' => 'Too many requests.'], 429);
    }

    /**
     * @param  array<string, mixed>|null  $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(?array $headers): array
    {
        if ($headers === null) {
            return [];
        }

        $normalized = [];
        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                continue;
            }
            $normalized[(string) $key] = (string) $value;
        }

        return $normalized;
    }

    private function truncate(?string $value, int $max): string
    {
        $value ??= '';
        if (strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, $max);
    }
}
