<?php

namespace App\Actions\Auth;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class VerifyAction
{
    public function execute(string $token): ?User
    {
        $pat = PersonalAccessToken::findToken($token);

        if (! $pat || $pat->expires_at?->isPast()) {
            return null;
        }

        return $pat->tokenable->load(['company', 'role.permissions']);
    }
}
