<?php

namespace App\Enums;

/** Voucher. Expired is not stored: it is Active + a past expires_at. */
enum VoucherStatus: string
{
    case Active = 'active';
    case Used = 'used';
    case Cancelled = 'cancelled';
}
