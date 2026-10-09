<?php

namespace App\Enums;

enum LivePlatform: string
{
    case Meet = 'meet';
    case Zoom = 'zoom';
    case Discord = 'discord';
    case Other = 'other';
}
