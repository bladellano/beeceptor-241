<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endpoint extends Model
{
    /** @use HasFactory<\Database\Factories\EndpointFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_active',
        'fallback_status',
        'fallback_headers',
        'fallback_body',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'fallback_status' => 'integer',
            'fallback_headers' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mockRules(): HasMany
    {
        return $this->hasMany(MockRule::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(RequestLog::class);
    }

    public function publicUrl(): string
    {
        return rtrim(config('app.url'), '/').'/'.$this->slug;
    }
}
