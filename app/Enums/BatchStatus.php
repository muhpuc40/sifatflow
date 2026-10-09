<?php

namespace App\Enums;

enum BatchStatus: string
{
    case Upcoming = 'upcoming';
    case Running = 'running';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
