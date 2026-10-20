<?php
namespace App\Services\Messaging\Senders;
use App\Services\Messaging\ChannelSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;


class SmsSender implements ChannelSender
{
    public function send(string $to, string $body, ?string $subject = null): void
    {
        $this->call($this->normalize($to), $body);
    }

    public function sendBulk(array $numbers, string $body): int
    {
        $numbers = array_values(array_unique(array_map(fn($n) => $this->normalize($n), $numbers)));

        if (!$numbers) {
            return 0;
        }

        $this->call(implode(',', $numbers), $body);

        return count($numbers);
    }

    private function call(string $numbers, string $message): void
    {
        $message = trim($message);

        if (config('messaging.sms.driver') === 'log') {
            Log::channel('app_file')->info("SMS to {$numbers}: {$message}");
            return;
        }

        $response = Http::timeout(20)->withQueryParameters([
            'api_key' => config('messaging.sms.api_key'),
            'type' => 'text',
            'number' => $numbers,
            'senderid' => config('messaging.sms.sender_id'),
            'message' => $message,
        ])->post(config('messaging.sms.url'));

        if (!$response->successful() || (int) $response->json('response_code') !== 202) {
            throw new RuntimeException('SMS failed: ' . $response->body());
        }
    }

    private function normalize(string $number): string
    {
        $number = preg_replace('/\D+/', '', $number);

        return str_starts_with($number, '01') ? '88' . $number : $number;
    }
}