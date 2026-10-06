<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class LockApiLoginChallenge
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->input('challenge_id');
        if (!is_string($id) || strlen($id) > 100) {
            return $next($request);
        }
        try {
            return Cache::lock('login-step:' . hash('sha256', $id), 120)->block(
                5,
                fn() => $next($request)
            );
        } catch (LockTimeoutException $e) {
            return response()->json(['message' => 'Another verification request is in progress. Try again.'], 429);
        }
    }
}
