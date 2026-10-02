<?php

namespace App\Http\Middleware;

use App\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;

/** Keeps user_devices.last_used_at fresh (at most once a minute) for "online now". */
class TrackDeviceActivity
{
    public function handle(Request $request, Closure $next)
    {
        $tokenId = $request->user()?->currentAccessToken()?->id;

        if ($tokenId) {
            UserDevice::where('token_id', $tokenId)
                ->where(fn ($q) => $q->whereNull('last_used_at')->orWhere('last_used_at', '<', now()->subMinute()))
                ->update(['last_used_at' => now()]);
        }

        return $next($request);
    }
}
