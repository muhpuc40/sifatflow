<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LockAdminLoginChallenge
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->session()->get('admin_login.challenge_id');

        if (!is_string($id)) {
            return $next($request);
        }

        try {
            return Cache::lock('login-step:' . hash('sha256', $id), 120)
                ->block(5, fn() => $next($request));
        } catch (LockTimeoutException) {
            $field = $request->routeIs('admin.login.send-code') ? 'channel' : 'code';

            return back()->withErrors([$field => 'Another request is in progress. Try again.']);
        }
    }
}