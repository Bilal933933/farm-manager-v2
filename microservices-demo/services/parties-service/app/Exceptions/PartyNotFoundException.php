<?php

namespace App\Exceptions;

use Exception;

class PartyNotFoundException extends Exception
{
    public function __construct(string $partyId = '')
    {
        parent::__construct("Party not found: {$partyId}");
    }
}
