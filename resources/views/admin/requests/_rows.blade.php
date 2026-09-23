@foreach ($requests as $log)
    <tr>
        <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
        <td class="method">{{ $log->method }}</td>
        <td><a href="{{ route('admin.endpoints.requests.show', [$endpoint, $log]) }}">{{ $log->path }}</a></td>
        <td>{{ $log->response_status }}</td>
        <td><code>{{ $log->ip_address ?? '—' }}</code></td>
        <td>{{ $log->matchedRule?->name ?? '—' }}</td>
        <td>{{ $log->fallback_used ? 'Yes' : 'No' }}</td>
        <td>{{ $log->duration_ms }} ms</td>
    </tr>
@endforeach
