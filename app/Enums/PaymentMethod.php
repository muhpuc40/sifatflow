<?php

namespace App\Enums;

/** How a student pays (and how an affiliate is paid): mobile banking, cash or bank. */
enum PaymentMethod: string
{
    case Mfs = 'mfs';
    case Cash = 'cash';
    case Bank = 'bank';
}
