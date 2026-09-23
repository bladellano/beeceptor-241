<?php

namespace App\Models;

use App\Enums\PathMatchType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockRule extends Model
{
    /** @use HasFactory<\Database\Factories\MockRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'name',
        'priority',
        'method',
        'path_pattern',
        'path_match_type',
        'query_conditions',
        'header_conditions',
        'body_conditions',
        'response_status',
        'response_headers',
        'response_body',
        'response_delay',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'path_match_type' => PathMatchType::class,
            'query_conditions' => 'array',
            'header_conditions' => 'array',
            'body_conditions' => 'array',
            'response_status' => 'integer',
            'response_headers' => 'array',
            'response_delay' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }
}
