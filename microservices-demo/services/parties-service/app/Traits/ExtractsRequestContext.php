<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ExtractsRequestContext
{
    protected function getCompanyId(Request $request): ?string
    {
        $value = $request->attributes->get('company_id');

        return $value ? (string) $value : null;
    }

    protected function getUserId(Request $request): ?string
    {
        $value = $request->attributes->get('user_id');

        return $value ? (string) $value : null;
    }

    protected function hasPermission(Request $request, string $perm): bool
    {
        return in_array($perm, $request->attributes->get('permissions', []));
    }
}
