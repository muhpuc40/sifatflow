<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SendComposedEmail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public string $email,
        public string $subject,
        public string $body,
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
        public ?string $attachmentMime = null,
        public ?int $adminId = null,
        public string $recipientType = 'custom',
    ) {
    }

    public function backoff(): array
    {
        return [30, 120];
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Mail::html($this->body, function ($message) {
            $message->to($this->email)->subject($this->subject);

            if ($this->attachmentPath && Storage::disk('local')->exists($this->attachmentPath)) {
                $message->attach(Storage::disk('local')->path($this->attachmentPath), [
                    'as' => $this->attachmentName,
                    'mime' => $this->attachmentMime,
                ]);
            }
        });

        Log::channel('app_file')->info('Email delivered', [
            'admin_id' => $this->adminId,
            'recipient_type' => $this->recipientType,
            'subject' => $this->subject,
            'recipient' => $this->email,
            'attachment' => $this->attachmentName,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('app_file')->error('Email failed', [
            'admin_id' => $this->adminId,
            'recipient_type' => $this->recipientType,
            'subject' => $this->subject,
            'recipient' => $this->email,
            'attachment' => $this->attachmentName,
            'error' => $exception->getMessage(),
        ]);
    }
}
