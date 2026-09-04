<?php

namespace App\Enums;

enum MovementType: string
{
    case HarvestIn = 'harvest_in';
    case PurchaseIn = 'purchase_in';
    case SaleOut = 'sale_out';
    case AdjustmentIn = 'adjustment_in';
    case AdjustmentOut = 'adjustment_out';
}
