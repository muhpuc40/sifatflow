<?php

namespace App\Enums;

/** One due of a course (order_item_installments). */
enum InstallmentStatus: string
{
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}
