<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEndpointRequest;
use App\Http\Requests\UpdateEndpointRequest;
use App\Models\Endpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EndpointController extends Controller
{
    use AuthorizesEndpoints;

    public function index(): View
    {
        $endpoints = Endpoint::query()
            ->where('user_id', auth()->id())
            ->withCount(['mockRules', 'requestLogs'])
            ->latest()
            ->paginate(20);

        return view('admin.endpoints.index', compact('endpoints'));
    }

    public function create(): View
    {
        return view('admin.endpoints.create');
    }

    public function store(StoreEndpointRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['fallback_status'] ??= config('mock.fallback_status');
        $data['fallback_headers'] ??= config('mock.fallback_headers');
        $data['fallback_body'] ??= config('mock.fallback_body');
        $data['is_active'] ??= true;

        $endpoint = Endpoint::query()->create($data);

        return redirect()->route('admin.endpoints.show', $endpoint)
            ->with('status', 'Endpoint created.');
    }

    public function show(Endpoint $endpoint): View|RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);

        $endpoint->loadCount(['mockRules', 'requestLogs']);
        $endpoint->load(['mockRules' => fn ($query) => $query->orderBy('priority')]);

        return view('admin.endpoints.show', compact('endpoint'));
    }

    public function edit(Endpoint $endpoint): View
    {
        $this->ensureOwnsEndpoint($endpoint);

        return view('admin.endpoints.edit', compact('endpoint'));
    }

    public function update(UpdateEndpointRequest $request, Endpoint $endpoint): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);

        $endpoint->update($request->validated());

        return redirect()->route('admin.endpoints.show', $endpoint)
            ->with('status', 'Endpoint updated.');
    }

    public function destroy(Endpoint $endpoint): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);

        $endpoint->delete();

        return redirect()->route('admin.endpoints.index')
            ->with('status', 'Endpoint deleted.');
    }

    public function toggle(Endpoint $endpoint): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);

        $endpoint->update(['is_active' => ! $endpoint->is_active]);

        return back()->with('status', 'Endpoint status updated.');
    }
}
