@extends('layouts.admin')

@section('title', 'Endpoints')

@section('content')
    <div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
        <h1>Endpoints</h1>
        <a class="btn btn-primary" href="{{ route('admin.endpoints.create') }}">New endpoint</a>
    </div>
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Public URL</th>
            <th>Rules</th>
            <th>Requests</th>
            <th>Created</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($endpoints as $endpoint)
            <tr>
                <td><a href="{{ route('admin.endpoints.show', $endpoint) }}">{{ $endpoint->name }}</a></td>
                <td><code>{{ $endpoint->slug }}</code></td>
                <td>
                    @if ($endpoint->is_active)
                        <span class="badge badge-ok">Active</span>
                    @else
                        <span class="badge badge-off">Inactive</span>
                    @endif
                </td>
                <td><code>{{ $endpoint->publicUrl() }}</code></td>
                <td>{{ $endpoint->mock_rules_count }}</td>
                <td>{{ $endpoint->request_logs_count }}</td>
                <td>{{ $endpoint->created_at->format('Y-m-d') }}</td>
                <td class="actions">
                    <a class="btn" href="{{ route('admin.endpoints.edit', $endpoint) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.endpoints.toggle', $endpoint) }}">@csrf @method('PATCH')<button class="btn" type="submit">Toggle</button></form>
                    <form method="POST" action="{{ route('admin.endpoints.destroy', $endpoint) }}" onsubmit="return confirm('Delete endpoint?')">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $endpoints->links() }}
@endsection
