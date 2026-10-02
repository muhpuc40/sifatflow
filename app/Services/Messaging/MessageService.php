<?php

namespace App\Services\Messaging;

use App\Enums\MessageChannel;

/**
 * One place to send email or SMS from anywhere in the project:
 *
 *   app(MessageService::class)->send(
 *       MessageChannel::Sms, $user->phone, 'login-code', ['code' => '123456'], 'Subject'
 *   );
 *
 * The text comes from resources/views/messages/{template}/{channel}.blade.php
 */
class MessageService
{
    public function send(
        MessageChannel $channel,
        string $to,
        string $template,
        array $data = [],
        ?string $subject = null
    ): void {
        $body = view("messages.{$template}.{$channel->value}", $data)->render();

        $channel->sender()->send($to, $body, $subject);
    }
}
