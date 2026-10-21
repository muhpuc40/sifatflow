<?php

namespace App\Enums;

/** affiliate_attributions: "this student belongs to that affiliate". */
enum AttributionStatus: string
{
    case Open = 'open';
    case Converted = 'converted';
    case Expired = 'expired';
}
