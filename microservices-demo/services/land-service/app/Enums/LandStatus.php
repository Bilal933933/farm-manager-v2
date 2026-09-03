<?php

namespace App\Enums;

enum LandStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case UnderContract = 'under_contract';
}
