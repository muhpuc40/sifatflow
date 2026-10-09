<?php

namespace App\Enums;

enum ExamStatus: string
{
    case Scheduled = 'scheduled';
    case Running = 'running';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
}
