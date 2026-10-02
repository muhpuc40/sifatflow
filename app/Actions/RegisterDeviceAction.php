<?php

namespace App\Actions;

use App\Models\UserDevice;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class RegisterDeviceAction
{
    /**
     * Step 1 of login: is there a free device slot?
     * The limit comes from the user_type_device_limits table.
     * Throws a 409 response (with the active devices) when the limit is reached.
     */
    public function ensureSlot($user, string $deviceId, ?int $revokeDeviceId = null): void
    {
        // A device that is already active never needs a new slot
        if ($user->devices()->active()->where('device_id', $deviceId)->exists()) {
            return;
        }

        $active = $user->devices()->active()->count();

        // The user chose an old device to replace. It is removed after the code is verified.
        if ($revokeDeviceId && $user->devices()->active()->whereKey($revokeDeviceId)->exists()) {
            $active--;
        }

        if ($active >= $user->deviceLimit()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Device limit reached. Remove one device to continue.',
                'limit' => $user->deviceLimit(),
                'devices' => $user->devices()->active()->get(['id', 'device_name', 'platform', 'browser', 'last_used_at']),
            ], 409));
        }
    }

    /** Last step of login (after the code is verified): save the device. */
    public function register($user, array $device, Request $request): UserDevice
    {
        $this->ensureSlot($user, $device['device_id']);

        return $user->devices()->updateOrCreate(
            ['device_id' => $device['device_id']],
            [
                'device_name' => $device['device_name'] ?? null,
                'platform' => $device['platform'] ?? null,
                'browser' => $device['browser'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_used_at' => now(),
                'revoked_at' => null,
            ]
        );
    }
}
