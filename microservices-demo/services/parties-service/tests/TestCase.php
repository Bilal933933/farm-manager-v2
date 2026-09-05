<?php

namespace Tests;

use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * Build service authentication headers for API requests.
     *
     * @param  array<int, string>  $perms
     * @return array<string, string>
     */
    protected function serviceHeaders(string $companyId = 'comp-1', string $userId = 'user-1', array $perms = []): array
    {
        return [
            'X-Service-Token' => config('app.service_token'),
            'X-Company-Id' => $companyId,
            'X-User-Id' => $userId,
            'X-Permissions' => implode(',', $perms),
        ];
    }

    /**
     * Create a party for testing.
     */
    protected function createParty(string $companyId = 'comp-1', array $attributes = []): Party
    {
        return Party::factory()
            ->forCompany($companyId)
            ->create($attributes);
    }

    /**
     * Create a party role for testing.
     */
    protected function createPartyRole(Party $party, array $attributes = []): PartyRole
    {
        return PartyRole::factory()
            ->forParty($party)
            ->create($attributes);
    }

    /**
     * Assert that the response has a valid JSON structure.
     */
    protected function assertValidJsonStructure(TestResponse $response, array $structure): void
    {
        $response->assertJsonStructure($structure);
    }

    /**
     * Assert that the response contains pagination metadata.
     */
    protected function assertHasPaginationStructure(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
    }

    /**
     * Assert that the response is a successful creation (201).
     */
    protected function assertCreated(TestResponse $response): void
    {
        $response->assertStatus(201);
    }

    /**
     * Assert that the response is forbidden (403).
     */
    protected function assertForbidden(TestResponse $response): void
    {
        $response->assertStatus(403);
    }

    /**
     * Assert that the response is unauthorized (401).
     */
    protected function assertUnauthorized(TestResponse $response): void
    {
        $response->assertStatus(401);
    }

    /**
     * Assert that the response has validation errors.
     */
    protected function assertHasValidationErrors(TestResponse $response, array $keys = []): void
    {
        $response->assertStatus(422);

        if (! empty($keys)) {
            $response->assertJsonValidationErrors($keys);
        }
    }
}
