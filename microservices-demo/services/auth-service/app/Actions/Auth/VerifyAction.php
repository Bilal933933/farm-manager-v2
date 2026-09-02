<?php

namespace App\Actions\Auth;

use App\Models\User;

class VerifyAction
{
    public function execute(User $user): User
    {
        return $user->load(['company', 'role.permissions']);
    }
}
