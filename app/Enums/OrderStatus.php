<?php

namespace App\Enums;

/** A whole checkout. */
enum OrderStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}
