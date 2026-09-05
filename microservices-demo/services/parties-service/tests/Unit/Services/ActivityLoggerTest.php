<?php

use App\Models\ActivityLog;
use App\Models\Party;
use App\Services\ActivityLogger;

describe('ActivityLogger', function () {
    test('log custom activity', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        $log = ActivityLogger::log(
            $userId,
            $companyId,
            $party,
            'custom_event',
            ['field' => 'value'],
            'Custom description'
        );

        expect($log)->toBeInstanceOf(ActivityLog::class);
        expect($log->event)->toBe('custom_event');
        expect($log->description)->toBe('Custom description');
        expect($log->changes)->toHaveKey('field');
    });

    test('log party created', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        $log = ActivityLogger::partyCreated($userId, $companyId, $party);

        expect($log->event)->toBe('created');
        expect($log->description)->toBe('Party created');
    });

    test('log party updated', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();
        $changes = ['name' => ['old' => 'Old', 'new' => 'New']];

        $log = ActivityLogger::partyUpdated($userId, $companyId, $party, $changes);

        expect($log->event)->toBe('updated');
        expect($log->changes)->toBe($changes);
    });

    test('log party deleted', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        $log = ActivityLogger::partyDeleted($userId, $companyId, $party);

        expect($log->event)->toBe('deleted');
        expect($log->description)->toBe('Party deleted');
    });

    test('log role created', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();
        $role = $party->roles()->create(['type' => 'supplier']);

        $log = ActivityLogger::roleCreated($userId, $companyId, $role);

        expect($log->event)->toBe('created');
        expect($log->description)->toBe('Role added to party');
    });

    test('log role deleted', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();
        $role = $party->roles()->create(['type' => 'supplier']);

        $log = ActivityLogger::roleDeleted($userId, $companyId, $role);

        expect($log->event)->toBe('deleted');
        expect($log->description)->toBe('Role removed from party');
    });

    test('retrieve logs by company', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        ActivityLogger::partyCreated($userId, $companyId, $party);

        $logs = ActivityLog::forCompany($companyId)->get();

        expect($logs)->toHaveCount(1);
    });

    test('retrieve logs by user', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        ActivityLogger::partyCreated($userId, $companyId, $party);

        $logs = ActivityLog::byUser($userId)->get();

        expect($logs)->toHaveCount(1);
    });

    test('retrieve logs by event', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        ActivityLogger::partyCreated($userId, $companyId, $party);

        $logs = ActivityLog::byEvent('created')->get();

        expect($logs->count())->toBeGreaterThan(0);
    });

    test('retrieve recent activities', function () {
        $companyId = fake()->uuid();
        $userId = fake()->uuid();
        $party = Party::factory()->forCompany($companyId)->create();

        ActivityLogger::partyCreated($userId, $companyId, $party);

        $logs = ActivityLog::recent()->get();

        expect($logs->count())->toBeGreaterThan(0);
    });
});
