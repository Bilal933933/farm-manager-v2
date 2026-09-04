<?php

namespace App\Enums;

enum PartyRoleType: string
{
    case Supplier = 'supplier';
    case Farmer = 'farmer';
    case Owner = 'owner';
    case Tenant = 'tenant';
    case Buyer = 'buyer';
    case Lessor = 'lessor';
    case Contractor = 'contractor';
}
