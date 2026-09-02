<?php

namespace App\Actions\Auth;

use App\Models\User;

class MeAction
{
    public function execute(User $user): User
    {
        return $user->load(['company', 'role.permissions']);
    }
}
