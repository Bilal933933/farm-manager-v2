<?php

use App\Enums\PartyStatus;
use App\Models\Party;
use App\Models\PartyRole;

// Scope Tests
test('scope active returns only active parties', function () {
    Party::factory()->active()->count(3)->create();
    Party::factory()->inactive()->count(2)->create();

    $active = Party::active()->get();

    expect($active)->toHaveCount(3)
        ->and($active->every(fn ($party) => $party->status === PartyStatus::Active))->toBeTrue();
});

test('scope inactive returns only inactive parties', function () {
    Party::factory()->active()->count(3)->create();
    Party::factory()->inactive()->count(2)->create();

    $inactive = Party::inactive()->get();

    expect($inactive)->toHaveCount(2)
        ->and($inactive->every(fn ($party) => $party->status === PartyStatus::Inactive))->toBeTrue();
});

test('scope for company filters by company id', function () {
    Party::factory()->forCompany('company-1')->count(3)->create();
    Party::factory()->forCompany('company-2')->count(2)->create();

    $company1Parties = Party::forCompany('company-1')->get();

    expect($company1Parties)->toHaveCount(3)
        ->and($company1Parties->every(fn ($party) => $party->company_id === 'company-1'))->toBeTrue();
});

test('scope search finds parties by name', function () {
    Party::factory()->create(['name' => 'Ahmed Farm']);
    Party::factory()->create(['name' => 'Mohamed Trading']);
    Party::factory()->create(['name' => 'Salem Agriculture']);

    $results = Party::search('Ahmed')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Ahmed Farm');
});

test('scope search finds parties by phone', function () {
    Party::factory()->create(['phone' => '01011111111']);
    Party::factory()->create(['phone' => '01022222222']);

    $results = Party::search('01011111111')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->phone)->toBe('01011111111');
});

test('scope search finds parties by email', function () {
    Party::factory()->create(['email' => 'ahmed@example.com']);
    Party::factory()->create(['email' => 'mohamed@example.com']);

    $results = Party::search('ahmed@example.com')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->email)->toBe('ahmed@example.com');
});

test('scope with role filters parties by role', function () {
    $party1 = Party::factory()->create();
    $party2 = Party::factory()->create();
    $party3 = Party::factory()->create();

    PartyRole::factory()->forParty($party1)->supplier()->create();
    PartyRole::factory()->forParty($party2)->farmer()->create();
    PartyRole::factory()->forParty($party3)->supplier()->create();

    $suppliers = Party::withRole('supplier')->get();

    expect($suppliers)->toHaveCount(2);
});

test('scope order by name sorts correctly', function () {
    Party::factory()->create(['name' => 'Zaid']);
    Party::factory()->create(['name' => 'Ahmed']);
    Party::factory()->create(['name' => 'Mohamed']);

    $parties = Party::orderByName('asc')->get();

    expect($parties->first()->name)->toBe('Ahmed')
        ->and($parties->last()->name)->toBe('Zaid');
});

test('scope latest orders by creation date descending', function () {
    $first = Party::factory()->create(['created_at' => now()->subDays(3)]);
    $second = Party::factory()->create(['created_at' => now()->subDays(1)]);
    $third = Party::factory()->create(['created_at' => now()]);

    $parties = Party::latest()->get();

    expect($parties->first()->id)->toBe($third->id);
});

// Helper Method Tests
test('isActive returns true for active party', function () {
    $party = Party::factory()->active()->create();

    expect($party->isActive())->toBeTrue();
});

test('isInactive returns true for inactive party', function () {
    $party = Party::factory()->inactive()->create();

    expect($party->isInactive())->toBeTrue();
});

test('hasRole returns true when party has specific role', function () {
    $party = Party::factory()->create();
    PartyRole::factory()->forParty($party)->supplier()->create();

    expect($party->hasRole('supplier'))->toBeTrue()
        ->and($party->hasRole('farmer'))->toBeFalse();
});

test('activate changes party status to active', function () {
    $party = Party::factory()->inactive()->create();

    $party->activate();

    expect($party->status)->toBe(PartyStatus::Active)
        ->and($party->isActive())->toBeTrue();
});

test('deactivate changes party status to inactive', function () {
    $party = Party::factory()->active()->create();

    $party->deactivate();

    expect($party->status)->toBe(PartyStatus::Inactive)
        ->and($party->isInactive())->toBeTrue();
});

test('get full contact attribute formats contact info correctly', function () {
    $party = Party::factory()->create([
        'name' => 'Ahmed Farm',
        'phone' => '01011111111',
        'email' => 'ahmed@example.com',
    ]);

    expect($party->full_contact)->toBe('Ahmed Farm | 01011111111 | ahmed@example.com');
});

test('get full contact attribute works without email', function () {
    $party = Party::factory()->withoutEmail()->create([
        'name' => 'Ahmed Farm',
        'phone' => '01011111111',
    ]);

    expect($party->full_contact)->toBe('Ahmed Farm | 01011111111');
});

// Relationship Tests
test('party has many roles', function () {
    $party = Party::factory()->create();
    PartyRole::factory()->forParty($party)->supplier()->create();
    PartyRole::factory()->forParty($party)->farmer()->create();

    expect($party->roles)->toHaveCount(2)
        ->and($party->roles->first())->toBeInstanceOf(PartyRole::class);
});

// Observer Tests
test('party observer sets default status on creation', function () {
    $party = Party::factory()->create(['status' => null]);

    expect($party->status)->not->toBeNull();
});
