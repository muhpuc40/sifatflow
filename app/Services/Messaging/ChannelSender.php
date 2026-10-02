<?php

namespace App\Services\Messaging;

interface ChannelSender
{
    /** Throw an exception if the message could not be sent. */
    public function send(string $to, string $body, ?string $subject = null): void;
}
