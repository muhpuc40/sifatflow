<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** A suspended admin loses the panel session on the next request. */
class EnsureAdminIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $admin = $request->user();

        if ($admin && ! $admin->isActive()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['login' => 'Your account is not active.']);
        }

        return $next($request);
    }
}
