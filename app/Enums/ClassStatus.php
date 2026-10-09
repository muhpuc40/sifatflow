<?php

namespace App\Enums;

enum ClassStatus: string
{
    case Scheduled = 'scheduled';
    case Held = 'held';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';
}
