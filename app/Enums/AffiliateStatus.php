<?php

namespace App\Enums;

/** Affiliate program status (affiliate_profiles). */
enum AffiliateStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
}
