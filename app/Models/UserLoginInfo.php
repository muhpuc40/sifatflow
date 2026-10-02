<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserLoginInfo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_type', 'user_id', 'login_identifier', 'status',
        'failure_reason', 'device_id', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }
}
