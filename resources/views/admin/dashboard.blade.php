@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <div class="grid">
        <div class="card"><div class="stat">{{ $endpointCount }}</div>Endpoints</div>
        <div class="card"><div class="stat">{{ $requestCount }}</div>Total requests</div>
        <div class="card"><div class="stat">{{ $requestsLast24h }}</div>Last 24 hours</div>
    </div>

    <div class="card">
        <h2>Most used endpoints</h2>
        <table>
            <thead><tr><th>Name</th><th>Slug</th><th>Requests</th></tr></thead>
            <tbody>
            @forelse ($topEndpoints as $endpoint)
                <tr>
                    <td><a href="{{ route('admin.endpoints.show', $endpoint) }}">{{ $endpoint->name }}</a></td>
                    <td><code>{{ $endpoint->slug }}</code></td>
                    <td>{{ $endpoint->request_logs_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No endpoints yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Latest requests</h2>
        <table>
            <thead><tr><th>Time</th><th>Endpoint</th><th>Method</th><th>Path</th><th>Status</th><th>Origem</th></tr></thead>
            <tbody>
            @forelse ($latestRequests as $log)
                <tr>
                    <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->endpoint->slug }}</td>
                    <td class="method">{{ $log->method }}</td>
                    <td><a href="{{ route('admin.endpoints.requests.show', [$log->endpoint, $log]) }}">{{ $log->path }}</a></td>
                    <td>{{ $log->response_status }}</td>
                    <td><code>{{ $log->ip_address ?? '—' }}</code></td>
                </tr>
            @empty
                <tr><td colspan="6">No requests captured yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
