<?php

namespace App\Exceptions;

use Exception;

class DuplicatePartyRoleException extends Exception
{
    public function __construct(string $role = '')
    {
        parent::__construct("Party already has role: {$role}");
    }
}
