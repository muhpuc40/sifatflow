<?php

namespace App\Jobs;

use App\Services\Messaging\Senders\SmsSender;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/** One chunk of recipients, sent in a single BulkSMSBD "one to many" call. */
class SendQuickSms implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public array $numbers,
        public string $message,
        public ?int $adminId = null,
        public string $recipientType = 'custom',
    ) {
    }

    public function backoff(): array
    {
        return [30, 120];
    }

    public function handle(SmsSender $sms): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $sent = $sms->sendBulk($this->numbers, $this->message);

        Log::channel('app_file')->info('SMS chunk delivered', [
            'admin_id' => $this->adminId,
            'recipient_type' => $this->recipientType,
            'numbers' => count($this->numbers),
            'sent' => $sent,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('app_file')->error('SMS chunk failed', [
            'admin_id' => $this->adminId,
            'recipient_type' => $this->recipientType,
            'numbers' => count($this->numbers),
            'error' => $exception->getMessage(),
        ]);
    }
}