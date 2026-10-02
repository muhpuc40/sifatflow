<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypeDeviceLimit extends Model
{
    protected $fillable = ['user_type', 'max_devices'];

    /** Max devices for a user type. Falls back to 2 if the row is missing. */
    public static function limitFor(string $userType): int
    {
        return (int) (static::where('user_type', $userType)->value('max_devices') ?? 2);
    }
}
