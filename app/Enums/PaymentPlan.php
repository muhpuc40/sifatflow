<?php

namespace App\Enums;

/** How one course in an order is paid. */
enum PaymentPlan: string
{
    case Full = 'full';
    case Installment = 'installment';
}
