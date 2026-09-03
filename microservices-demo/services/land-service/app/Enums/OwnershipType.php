<?php

namespace App\Enums;

enum OwnershipType: string
{
    case Owned = 'owned';
    case RentedIn = 'rented_in';
    case Shared = 'shared';
}
