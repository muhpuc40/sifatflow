<?php

namespace App\Enums;

enum AmountType: string
{
    case Percent = 'percent';
    case Flat = 'flat';
}
