@extends('layouts.admin')

@section('title', 'Requests')

@section('content')
    <div class="page-toolbar">
        <div>
            <h1>Requests — {{ $endpoint->name }}</h1>
            <p><a href="{{ route('admin.endpoints.show', $endpoint) }}">&larr; Back to endpoint</a></p>
        </div>
        <div class="refresh-control">
            <label for="requests-refresh-interval">Refresh</label>
            <select
                id="requests-refresh-interval"
                data-poll-url="{{ route('admin.endpoints.requests.index', $endpoint) }}"
            >
                <option value="0">Off</option>
                <option value="5">5s</option>
                <option value="10">10s</option>
                <option value="15">15s</option>
                <option value="30">30s</option>
                <option value="60">60s</option>
            </select>
            <span id="requests-refresh-status" class="refresh-status" aria-live="polite"></span>
        </div>
    </div>
    <table>
        <thead><tr><th>Time</th><th>Method</th><th>Path</th><th>Status</th><th>Rule</th><th>Fallback</th><th>Duration</th></tr></thead>
        <tbody id="requests-table-body">
            @include('admin.requests._rows')
        </tbody>
    </table>
    {{ $requests->links() }}
@endsection

@push('scripts')
    <script src="{{ \App\Support\AssetVersion::url('js/requests-refresh.js') }}" defer></script>
@endpush
