<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesEndpoints;
use App\Http\Controllers\Controller;
use App\Models\Endpoint;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestLogController extends Controller
{
    use AuthorizesEndpoints;

    public function index(Request $request, Endpoint $endpoint): View
    {
        $this->ensureOwnsEndpoint($endpoint);

        $requests = $endpoint->requestLogs()
            ->with('matchedRule')
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.requests._rows', compact('endpoint', 'requests'));
        }

        return view('admin.requests.index', compact('endpoint', 'requests'));
    }

    public function show(Endpoint $endpoint, RequestLog $requestLog): View
    {
        $this->ensureOwnsEndpoint($endpoint);
        abort_if($requestLog->endpoint_id !== $endpoint->id, 403);

        $requestLog->load('matchedRule');

        return view('admin.requests.show', compact('endpoint', 'requestLog'));
    }
}
