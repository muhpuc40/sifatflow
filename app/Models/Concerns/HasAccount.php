<?php

namespace App\Models\Concerns;

use App\Enums\UserStatus;
use App\Models\UserDevice;
use App\Models\UserLoginInfo;
use App\Models\UserTypeDeviceLimit;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Shared by Admin, Student and Instructor.
 * user_devices and user_login_infos use user_type + user_id,
 * where user_type is the morph alias: admin | student | instructor.
 */
trait HasAccount
{
    use HasApiTokens;

    public static function bootHasAccount(): void
    {
        static::creating(function ($user) {
            $user->public_id ??= (string) Str::ulid();
        });
    }

    public function devices(): MorphMany
    {
        return $this->morphMany(UserDevice::class, 'user', 'user_type', 'user_id');
    }

    public function loginInfos(): MorphMany
    {
        return $this->morphMany(UserLoginInfo::class, 'user', 'user_type', 'user_id');
    }

    public function userType(): string
    {
        return $this->getMorphClass();
    }

    public function deviceLimit(): int
    {
        return UserTypeDeviceLimit::limitFor($this->userType());
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }
}
