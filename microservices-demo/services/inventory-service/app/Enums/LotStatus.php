<?php

namespace App\Enums;

enum LotStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case SoldOut = 'sold_out';
    case Expired = 'expired';
}
