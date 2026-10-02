<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** A student token cannot open /instructor/... routes, and so on. */
class EnsureUserType
{
    public function handle(Request $request, Closure $next)
    {
        // abort_unless(
        //     $request->user()?->userType() === $request->route('type'),
        //     403,
        //     'Wrong account type.'
        // );

        //A suspended account keeps working until its token is removed. auth:sanctum only checks that the token exists, not the account status. Change EnsureUserType.php so it also checks the status:

        $user = $request->user();

        abort_unless(
            $user && $user->isActive() && $user->userType() === $request->route('type'),
            403,
            'Wrong account type.'
        );
        return $next($request);
    }
}
