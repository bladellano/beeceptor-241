<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateMockRequestSize
{
    public function handle(Request $request, Closure $next): Response
    {
        $max = config('mock.max_request_body_size', 1048576);
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);

        if ($contentLength > $max) {
            return response()->json(['message' => 'Request body too large.'], 413);
        }

        return $next($request);
    }
}
