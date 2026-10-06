<?php

namespace App\Enums;

enum ItemType: string
{
    case Recorded = 'recorded';
    case Live = 'live';
    case Exam = 'exam';
    case Assignment = 'assignment';
    case Resources = 'resources';
}
