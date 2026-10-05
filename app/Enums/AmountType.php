<?php

namespace App\Enums;

/** How an amount is read: a percentage of the price, or a flat taka value. */
enum AmountType: string
{
    case Percent = 'percent';
    case Flat = 'flat';
}
