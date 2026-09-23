<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Endpoint;
use App\Models\RequestLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $userId = auth()->id();

        $endpointCount = Endpoint::query()->where('user_id', $userId)->count();
        $requestCount = RequestLog::query()
            ->whereIn('endpoint_id', Endpoint::query()->where('user_id', $userId)->select('id'))
            ->count();
        $requestsLast24h = RequestLog::query()
            ->whereIn('endpoint_id', Endpoint::query()->where('user_id', $userId)->select('id'))
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $topEndpoints = Endpoint::query()
            ->where('user_id', $userId)
            ->withCount('requestLogs')
            ->orderByDesc('request_logs_count')
            ->limit(5)
            ->get();

        $latestRequests = RequestLog::query()
            ->with(['endpoint', 'matchedRule'])
            ->whereIn('endpoint_id', Endpoint::query()->where('user_id', $userId)->select('id'))
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'endpointCount',
            'requestCount',
            'requestsLast24h',
            'topEndpoints',
            'latestRequests',
        ));
    }
}
