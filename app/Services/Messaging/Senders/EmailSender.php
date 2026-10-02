<?php

namespace App\Services\Messaging\Senders;

use App\Services\Messaging\ChannelSender;
use Illuminate\Support\Facades\Mail;

/** Uses the mailer from .env (MAIL_MAILER=log for development, smtp for Gmail). */
class EmailSender implements ChannelSender
{
    public function send(string $to, string $body, ?string $subject = null): void
    {
        Mail::html($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject ?? config('app.name'));
        });
    }
}
