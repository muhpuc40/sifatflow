<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** A student token cannot open /instructor/... routes, and so on. */
class EnsureUserType
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $request->user()?->userType() === $request->route('type'),
            403,
            'Wrong account type.'
        );

        return $next($request);
    }
}
