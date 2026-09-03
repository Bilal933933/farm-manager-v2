<?php

namespace App\Enums;

enum CostType: string
{
    case Seeds = 'seeds';
    case Fertilizer = 'fertilizer';
    case Pesticides = 'pesticides';
    case Labor = 'labor';
    case Equipment = 'equipment';
    case Irrigation = 'irrigation';
    case Transport = 'transport';
    case Rent = 'rent';
    case Other = 'other';
}
