<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Runs on every admin page. The session is valid only while
 *   - the admin account is active, and
 *   - this browser's device (cookie sf_device) is still saved and not signed out.
 * So signing out a device (or suspending the admin) ends its session on the next request.
 */
class EnsureAdminIsActive
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = $request->user();

        if ($admin) {
            $deviceId = $request->cookie('sf_device');
            $device = is_string($deviceId)
                ? $admin->devices()->active()->where('device_id', $deviceId)->first()
                : null;

            if (!$admin->isActive() || !$device) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->withErrors([
                    'login' => $admin->isActive()
                        ? 'This device was signed out. Please sign in again.'
                        : 'Your account is not active.',
                ]);
            }

            // "Online now": update at most once a minute
            if (!$device->last_used_at || $device->last_used_at->lt(now()->subMinute())) {
                $device->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
