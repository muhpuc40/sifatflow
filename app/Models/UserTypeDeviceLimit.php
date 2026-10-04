<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypeDeviceLimit extends Model
{
    protected $fillable = ['user_type', 'max_devices'];

    /** A missing policy returns zero so login fails closed instead of silently allowing devices. */
    public static function limitFor(string $userType): int
    {
        return max(0, (int) static::where('user_type', $userType)->value('max_devices'));
    }
}
