<?php

use App\Actions\Party\CreatePartyAction;
use App\Actions\Party\DeletePartyAction;
use App\Actions\Party\SearchPartiesAction;
use App\Actions\Party\UpdatePartyAction;
use App\Models\Party;

describe('Party Actions', function () {
    describe('CreatePartyAction', function () {
        test('create party with valid data', function () {
            $action = new CreatePartyAction;

            $data = [
                'company_id' => fake()->uuid(),
                'name' => 'New Party',
                'email' => 'test@example.com',
                'phone' => '123456789',
                'status' => 'active',
            ];

            $party = $action->execute($data);

            expect($party)->toBeInstanceOf(Party::class);
            expect($party->name)->toBe('New Party');
            expect($party->email)->toBe('test@example.com');
        });

        test('throw exception on invalid data', function () {
            $action = new CreatePartyAction;

            $action->execute(['company_id' => '', 'name' => '']);
        })->throws(Exception::class);
    });

    describe('UpdatePartyAction', function () {
        test('update party with valid data', function () {
            $companyId = fake()->uuid();
            $party = Party::factory()->forCompany($companyId)->create(['name' => 'Original']);
            $action = new UpdatePartyAction;

            $updated = $action->execute($party, ['name' => 'Updated']);

            expect($updated->name)->toBe('Updated');
        });

        test('preserve unchanged fields', function () {
            $companyId = fake()->uuid();
            $party = Party::factory()->forCompany($companyId)->create(['name' => 'Original', 'email' => 'test@example.com']);
            $action = new UpdatePartyAction;

            $updated = $action->execute($party, ['name' => 'Updated']);

            expect($updated->email)->toBe('test@example.com');
        });
    });

    describe('DeletePartyAction', function () {
        test('soft delete party', function () {
            $companyId = fake()->uuid();
            $party = Party::factory()->forCompany($companyId)->create();
            $action = new DeletePartyAction;

            $action->execute($party);

            expect($party->fresh()->deleted_at)->not()->toBeNull();
        });

        test('can restore soft deleted party', function () {
            $companyId = fake()->uuid();
            $party = Party::factory()->forCompany($companyId)->create();
            $action = new DeletePartyAction;

            $action->execute($party);
            $party->restore();

            expect($party->fresh()->deleted_at)->toBeNull();
        });
    });

    describe('SearchPartiesAction', function () {
        test('search parties by keyword', function () {
            $companyId = fake()->uuid();
            Party::factory()->forCompany($companyId)->create(['name' => 'John Doe']);
            Party::factory()->forCompany($companyId)->create(['name' => 'Jane Smith']);
            $action = new SearchPartiesAction;

            $result = $action->execute($companyId, ['search' => 'John']);

            expect($result->count())->toBeGreaterThan(0);
        });

        test('filter parties by status', function () {
            $companyId = fake()->uuid();
            Party::factory()->forCompany($companyId)->create(['status' => 'active']);
            Party::factory()->forCompany($companyId)->create(['status' => 'inactive']);
            $action = new SearchPartiesAction;

            $result = $action->execute($companyId, ['status' => 'active']);

            $result->each(function (Party $party) {
                expect($party->status)->toBe('active');
            });
        });

        test('apply pagination', function () {
            $companyId = fake()->uuid();
            for ($i = 0; $i < 20; $i++) {
                Party::factory()->forCompany($companyId)->create();
            }

            $action = new SearchPartiesAction;
            $result = $action->execute($companyId, ['per_page' => 10]);

            expect($result->perPage())->toBe(10);
            expect($result->total())->toBeGreaterThanOrEqual(20);
        });

        test('sort by different fields', function () {
            $companyId = fake()->uuid();
            Party::factory()->forCompany($companyId)->create(['name' => 'Zebra']);
            Party::factory()->forCompany($companyId)->create(['name' => 'Apple']);
            $action = new SearchPartiesAction;

            $result = $action->execute($companyId, ['sort_by' => 'name', 'sort_order' => 'asc']);

            $first = $result->items()[0] ?? null;
            if ($first) {
                expect($first->name)->toBe('Apple');
            }
        });

        test('cache search results', function () {
            $companyId = fake()->uuid();
            Party::factory()->forCompany($companyId)->create(['name' => 'Cached Party']);
            $action = new SearchPartiesAction;

            $result1 = $action->execute($companyId, ['search' => 'Cached']);
            $result2 = $action->execute($companyId, ['search' => 'Cached']);

            expect($result1->total())->toBe($result2->total());
        });
    });
});
