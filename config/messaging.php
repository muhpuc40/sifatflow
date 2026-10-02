<?php

return [

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),   // log = write to laravel.log, bulksmsbd = send for real
        'url' => env('SMS_URL'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
    ],

    // Local development only: return the code in the send-code response (for Postman)
    'show_login_code' => env('OTP_SHOW_CODE', false),

];
