<?php

namespace App\Enums;

enum ContractType: string
{
    case RentIn = 'rent_in';
    case RentOut = 'rent_out';
    case Sharecropping = 'sharecropping';
    case Management = 'management';
}
