<?php

namespace App\Enums;

/** A payment sent by the student (payment_list). */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
