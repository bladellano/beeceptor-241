@extends('layouts.admin')

@section('title', $endpoint->name)

@section('content')
    <div class="actions" style="justify-content: space-between;">
        <h1>{{ $endpoint->name }}</h1>
        <div class="actions">
            <a class="btn" href="{{ route('admin.endpoints.edit', $endpoint) }}">Edit</a>
            <a class="btn btn-primary" href="{{ route('admin.endpoints.rules.create', $endpoint) }}">New rule</a>
        </div>
    </div>

    <div class="card">
        <p><strong>Slug:</strong> <code>{{ $endpoint->slug }}</code></p>
        <p><strong>Public URL:</strong> <code>{{ $endpoint->publicUrl() }}</code></p>
        <p><strong>Status:</strong> {{ $endpoint->is_active ? 'Active' : 'Inactive' }}</p>
        <p><strong>Description:</strong> {{ $endpoint->description ?: '—' }}</p>
        <p><strong>Fallback:</strong> HTTP {{ $endpoint->fallback_status }}</p>
    </div>

    <div class="card">
        <div class="actions" style="justify-content: space-between;">
            <h2>Rules ({{ $endpoint->mock_rules_count }})</h2>
        </div>
        <table>
            <thead><tr><th>Priority</th><th>Name</th><th>Method</th><th>Path</th><th>Match</th><th>Status</th><th>Response</th><th></th></tr></thead>
            <tbody>
            @forelse ($endpoint->mockRules as $rule)
                <tr>
                    <td>{{ $rule->priority }}</td>
                    <td>{{ $rule->name }}</td>
                    <td class="method">{{ $rule->method }}</td>
                    <td><code>{{ $rule->path_pattern }}</code></td>
                    <td>{{ $rule->path_match_type->value }}</td>
                    <td>{{ $rule->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $rule->response_status }}</td>
                    <td class="actions">
                        <a class="btn" href="{{ route('admin.endpoints.rules.edit', [$endpoint, $rule]) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.endpoints.rules.toggle', [$endpoint, $rule]) }}">@csrf @method('PATCH')<button class="btn" type="submit">Toggle</button></form>
                        <form method="POST" action="{{ route('admin.endpoints.rules.destroy', [$endpoint, $rule]) }}" onsubmit="return confirm('Delete rule?')">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">No rules yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="actions" style="justify-content: space-between;">
            <h2>Requests ({{ $endpoint->request_logs_count }})</h2>
            <a class="btn" href="{{ route('admin.endpoints.requests.index', $endpoint) }}">View all</a>
        </div>
    </div>
@endsection
