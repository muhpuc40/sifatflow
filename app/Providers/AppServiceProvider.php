<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Stored in the database as: admin | student | instructor
        // (user_devices.user_type, user_login_infos.user_type, personal_access_tokens.tokenable_type)
        Relation::enforceMorphMap([
            'admin' => Admin::class,
            'student' => Student::class,
            'instructor' => Instructor::class,
        ]);

        // Step 1: password attempts
        RateLimiter::for('login', fn (Request $request) =>
            Limit::perMinute(5)->by($request->ip().'|'.$request->input('login'))
        );

        // Steps 2 and 3: send code / verify code
        RateLimiter::for('otp', fn (Request $request) =>
            Limit::perMinute(10)->by($request->ip().'|'.$request->input('challenge_id'))
        );

        RateLimiter::for('password-reset', fn (Request $request) => [
            Limit::perMinute(20)->by('reset-ip:'.$request->ip()),
            Limit::perMinute(5)->by('reset-step:'.$request->ip().'|'.hash('sha256',
                json_encode([$request->route('type'), $request->input('login'), $request->input('challenge_id')]))),
        ]);

    }
}
