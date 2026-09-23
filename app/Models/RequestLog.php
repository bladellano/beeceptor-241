<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'endpoint_id',
        'method',
        'url',
        'path',
        'query_parameters',
        'headers',
        'body',
        'ip_address',
        'user_agent',
        'matched_rule_id',
        'fallback_used',
        'response_status',
        'response_headers',
        'response_body',
        'duration_ms',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'query_parameters' => 'array',
            'headers' => 'array',
            'fallback_used' => 'boolean',
            'response_headers' => 'array',
            'duration_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function matchedRule(): BelongsTo
    {
        return $this->belongsTo(MockRule::class, 'matched_rule_id');
    }
}
