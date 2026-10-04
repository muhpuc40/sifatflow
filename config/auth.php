<?php
return [
    'defaults' => ['guard' => env('AUTH_GUARD', 'web'), 'passwords' => null],
    'guards' => ['web' => ['driver' => 'session', 'provider' => 'admins']],
    'providers' => [
        'admins' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
        'students' => ['driver' => 'eloquent', 'model' => App\Models\Student::class],
        'instructors' => ['driver' => 'eloquent', 'model' => App\Models\Instructor::class],
    ],
    // This app uses a channel/code flow, NOT Laravel's email-link broker.
    // Its default repository identifies rows by email only and cannot isolate these three account types.
    'passwords' => [],
    'password_reset' => [
        'providers' => ['admin' => 'admins', 'student' => 'students', 'instructor' => 'instructors'],
        'challenge_ttl' => 600, 'code_ttl' => 300, 'reset_token_ttl' => 600,
        'resend_after' => 60, 'max_attempts' => 5, 'max_codes_per_hour' => 5,
    ],
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
