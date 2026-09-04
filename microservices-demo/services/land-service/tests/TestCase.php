<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param  array<int, string>  $perms
     * @return array<string, string>
     */
    protected function serviceHeaders(string $companyId = 'comp-1', array $perms = []): array
    {
        return [
            'X-Service-Token' => config('app.service_token'),
            'X-Company-Id' => $companyId,
            'X-User-Id' => 'user-1',
            'X-Permissions' => implode(',', $perms),
        ];
    }
}
