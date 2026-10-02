<?php

namespace App\Services\Messaging\Senders;

use App\Services\Messaging\ChannelSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/** BulkSMSBD. Settings are in config/messaging.php (values come from .env). */
class SmsSender implements ChannelSender
{
    public function send(string $to, string $body, ?string $subject = null): void
    {
        $number = $this->normalize($to);
        $message = trim($body);

        // Development: write the SMS to storage/logs/laravel.log instead of sending it
        if (config('messaging.sms.driver') === 'log') {
            Log::info("SMS to {$number}: {$message}");

            return;
        }

        $response = Http::asForm()->timeout(15)->post(config('messaging.sms.url'), [
            'api_key' => config('messaging.sms.api_key'),
            'type' => 'text',
            'number' => $number,
            'senderid' => config('messaging.sms.sender_id'),
            'message' => $message,
        ]);

        // BulkSMSBD answers with response_code 202 when the SMS is accepted
        if (! $response->successful() || $response->json('response_code') != 202) {
            throw new RuntimeException('SMS failed: '.$response->body());
        }
    }

    /** 01711000001 -> 8801711000001 */
    private function normalize(string $number): string
    {
        $number = preg_replace('/\D+/', '', $number);

        return str_starts_with($number, '01') ? '88'.$number : $number;
    }
}
