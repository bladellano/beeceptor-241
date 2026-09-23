<?php

return [
    'fallback_status' => 200,
    'fallback_body' => 'Hey ya! Great to see you here. Btw, nothing is configured for this request path. Create a rule and start building a mock API.',
    'fallback_headers' => [
        'Content-Type' => 'text/plain; charset=UTF-8',
    ],

    'max_mock_delay_ms' => (int) env('MAX_MOCK_DELAY', 10000),

    'max_request_body_size' => (int) env('MAX_REQUEST_BODY_SIZE', 1048576),
    'max_response_body_size' => (int) env('MAX_RESPONSE_BODY_SIZE', 1048576),
    'max_log_header_size' => (int) env('MAX_LOG_HEADER_SIZE', 65536),

    'request_log_retention_days' => (int) env('REQUEST_LOG_RETENTION_DAYS', 7),

    'rate_limit' => (int) env('MOCK_RATE_LIMIT', 60),
    'rate_limit_window' => (int) env('MOCK_RATE_LIMIT_WINDOW', 1),

    'cors_allowed_origins' => env('CORS_ALLOWED_ORIGINS', '*'),
    'cors_allowed_methods' => env('CORS_ALLOWED_METHODS', '*'),
    'cors_allowed_headers' => env('CORS_ALLOWED_HEADERS', '*'),

    'reserved_slugs' => [
        'admin',
        'api',
        'login',
        'logout',
        'register',
        'up',
        'storage',
        'build',
        'sanctum',
    ],

    'inactive_endpoint_status' => 410,
];
