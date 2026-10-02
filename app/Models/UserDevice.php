<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken;

class UserDevice extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'token_id', 'device_id', 'device_name',
        'platform', 'browser', 'ip_address', 'user_agent', 'last_used_at', 'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    /** Active and used in the last N minutes ("online now"). */
    public function scopeOnline(Builder $query, int $minutes = 5): Builder
    {
        return $query->active()->where('last_used_at', '>=', now()->subMinutes($minutes));
    }

    /** Delete the device's token and free the slot. */
    public function revoke(): void
    {
        if ($this->token_id) {
            PersonalAccessToken::whereKey($this->token_id)->delete();
        }

        $this->update(['revoked_at' => now(), 'token_id' => null]);
    }
}
