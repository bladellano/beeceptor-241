<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMockRuleRequest;
use App\Http\Requests\UpdateMockRuleRequest;
use App\Models\Endpoint;
use App\Models\MockRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MockRuleController extends Controller
{
    use AuthorizesEndpoints;

    public function create(Endpoint $endpoint): View
    {
        $this->ensureOwnsEndpoint($endpoint);

        return view('admin.rules.create', compact('endpoint'));
    }

    public function store(StoreMockRuleRequest $request, Endpoint $endpoint): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);

        $endpoint->mockRules()->create($request->validated());

        return redirect()->route('admin.endpoints.show', $endpoint)
            ->with('status', 'Rule created.');
    }

    public function edit(Endpoint $endpoint, MockRule $rule): View
    {
        $this->ensureOwnsEndpoint($endpoint);
        abort_if($rule->endpoint_id !== $endpoint->id, 403);

        return view('admin.rules.edit', compact('endpoint', 'rule'));
    }

    public function update(UpdateMockRuleRequest $request, Endpoint $endpoint, MockRule $rule): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);
        abort_if($rule->endpoint_id !== $endpoint->id, 403);

        $rule->update($request->validated());

        return redirect()->route('admin.endpoints.show', $endpoint)
            ->with('status', 'Rule updated.');
    }

    public function destroy(Endpoint $endpoint, MockRule $rule): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);
        abort_if($rule->endpoint_id !== $endpoint->id, 403);

        $rule->delete();

        return redirect()->route('admin.endpoints.show', $endpoint)
            ->with('status', 'Rule deleted.');
    }

    public function toggle(Endpoint $endpoint, MockRule $rule): RedirectResponse
    {
        $this->ensureOwnsEndpoint($endpoint);
        abort_if($rule->endpoint_id !== $endpoint->id, 403);

        $rule->update(['is_active' => ! $rule->is_active]);

        return back()->with('status', 'Rule status updated.');
    }
}
