<?php

namespace App\Enums;

enum PaymentTerms: string
{
    case Annual = 'annual';
    case SemiAnnual = 'semi_annual';
    case Quarterly = 'quarterly';
    case Monthly = 'monthly';
    case LumpSum = 'lump_sum';
}
