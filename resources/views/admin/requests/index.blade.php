@extends('layouts.admin')

@section('title', 'Requests')

@section('content')
    <h1>Requests — {{ $endpoint->name }}</h1>
    <p><a href="{{ route('admin.endpoints.show', $endpoint) }}">&larr; Back to endpoint</a></p>
    <table>
        <thead><tr><th>Time</th><th>Method</th><th>Path</th><th>Status</th><th>Rule</th><th>Fallback</th><th>Duration</th></tr></thead>
        <tbody>
        @foreach ($requests as $log)
            <tr>
                <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                <td class="method">{{ $log->method }}</td>
                <td><a href="{{ route('admin.endpoints.requests.show', [$endpoint, $log]) }}">{{ $log->path }}</a></td>
                <td>{{ $log->response_status }}</td>
                <td>{{ $log->matchedRule?->name ?? '—' }}</td>
                <td>{{ $log->fallback_used ? 'Yes' : 'No' }}</td>
                <td>{{ $log->duration_ms }} ms</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $requests->links() }}
@endsection
