<?php

namespace App\Enums;

use App\Services\Messaging\ChannelSender;
use App\Services\Messaging\Senders\EmailSender;
use App\Services\Messaging\Senders\SmsSender;

/**
 * To add a new channel: add a case here and a sender class in
 * app/Services/Messaging/Senders. Add a view file per template: messages/{template}/{channel}.blade.php
 */
enum MessageChannel: string
{
    case Email = 'email';
    case Sms = 'sms';

    public function sender(): ChannelSender
    {
        return match ($this) {
            self::Email => app(EmailSender::class),
            self::Sms => app(SmsSender::class),
        };
    }
}
