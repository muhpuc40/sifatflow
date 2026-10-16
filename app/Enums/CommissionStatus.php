<?php

namespace App\Enums;

/** Commission of one conversion. */
enum CommissionStatus: string
{
    case Pending = 'pending';
    case Available = 'available';
    case Requested = 'requested';
    case Paid = 'paid';
    case Reversed = 'reversed';
}
