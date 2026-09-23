@php
    $formatJson = function (?string $value): string {
        if ($value === null || $value === '') {
            return '';
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        return $value;
    };
@endphp

@extends('layouts.admin')

@section('title', 'Request detail')

@section('content')
    <h1>Request detail</h1>
    <p><a href="{{ route('admin.endpoints.requests.index', $endpoint) }}">&larr; Back to requests</a></p>

    <div class="card">
        <h2>Request</h2>
        <p><strong>Method:</strong> <span class="method">{{ $requestLog->method }}</span></p>
        <p><strong>URL:</strong> {{ $requestLog->url }}</p>
        <p><strong>Path:</strong> <code>{{ $requestLog->path }}</code></p>
        <p><strong>IP:</strong> {{ $requestLog->ip_address }}</p>
        <p><strong>User Agent:</strong> {{ $requestLog->user_agent }}</p>
        <h3>Query parameters</h3>
        <pre class="json">{{ $formatJson(json_encode($requestLog->query_parameters)) }}</pre>
        <h3>Headers</h3>
        <pre class="json">{{ $formatJson(json_encode($requestLog->headers)) }}</pre>
        <h3>Body</h3>
        <pre class="json">{{ e($formatJson($requestLog->body)) }}</pre>
    </div>

    <div class="card">
        <h2>Matching</h2>
        <p><strong>Matched rule:</strong> {{ $requestLog->matchedRule?->name ?? 'None' }}</p>
        <p><strong>Priority:</strong> {{ $requestLog->matchedRule?->priority ?? '—' }}</p>
        <p><strong>Fallback used:</strong> {{ $requestLog->fallback_used ? 'Yes' : 'No' }}</p>
    </div>

    <div class="card">
        <h2>Response</h2>
        <p><strong>Status:</strong> {{ $requestLog->response_status }}</p>
        <p><strong>Duration:</strong> {{ $requestLog->duration_ms }} ms</p>
        <h3>Headers</h3>
        <pre class="json">{{ $formatJson(json_encode($requestLog->response_headers)) }}</pre>
        <h3>Body</h3>
        <pre class="json">{{ e($formatJson($requestLog->response_body)) }}</pre>
    </div>
@endsection
