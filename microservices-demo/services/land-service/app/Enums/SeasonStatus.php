<?php

namespace App\Enums;

enum SeasonStatus: string
{
    case Preparing = 'preparing';
    case Planted = 'planted';
    case Growing = 'growing';
    case Harvesting = 'harvesting';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
