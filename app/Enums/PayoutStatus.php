<?php

namespace App\Enums;

/** Payout to an affiliate. */
enum PayoutStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Paid = 'paid';
    case Rejected = 'rejected';
}
