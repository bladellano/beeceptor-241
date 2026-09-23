<?php

namespace App\Services;

use App\Models\Endpoint;
use App\Models\MockRule;
use App\Models\RequestLog;
use Illuminate\Http\Request;

class RequestLogService
{
    public function log(
        Endpoint $endpoint,
        Request $request,
        string $path,
        array $responsePayload,
        ?MockRule $matchedRule,
        bool $fallbackUsed,
        int $durationMs,
    ): RequestLog {
        return RequestLog::query()->create([
            'endpoint_id' => $endpoint->id,
            'method' => strtoupper($request->method()),
            'url' => $request->fullUrl(),
            'path' => $path,
            'query_parameters' => $request->query->all(),
            'headers' => $this->truncateHeaders($request->headers->all()),
            'body' => $this->truncateBody($request->getContent()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'matched_rule_id' => $matchedRule?->id,
            'fallback_used' => $fallbackUsed,
            'response_status' => $responsePayload['status'],
            'response_headers' => $responsePayload['headers'],
            'response_body' => $this->truncateBody($responsePayload['body']),
            'duration_ms' => $durationMs,
            'created_at' => now(),
        ]);
    }

    private function truncateBody(string $body): string
    {
        $max = config('mock.max_request_body_size');

        if (strlen($body) <= $max) {
            return $body;
        }

        return substr($body, 0, $max);
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     * @return array<string, array<int, string>>
     */
    private function truncateHeaders(array $headers): array
    {
        $encoded = json_encode($headers);
        $max = config('mock.max_log_header_size');

        if ($encoded !== false && strlen($encoded) <= $max) {
            return $headers;
        }

        return ['truncated' => ['true']];
    }
}
