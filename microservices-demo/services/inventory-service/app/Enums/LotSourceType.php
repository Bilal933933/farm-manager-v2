<?php

namespace App\Enums;

enum LotSourceType: string
{
    case Harvest = 'harvest';
    case Purchase = 'purchase';
    case Adjustment = 'adjustment';
}
