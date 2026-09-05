<?php

use App\Enums\PartyRoleType;
use App\Enums\PartyStatus;
use App\Models\Party;
use App\Models\PartyRole;

test('party factory creates valid party with default attributes', function () {
    $party = Party::factory()->create();

    expect($party)->toBeInstanceOf(Party::class)
        ->and($party->company_id)->not->toBeNull()
        ->and($party->name)->not->toBeNull()
        ->and($party->phone)->not->toBeNull()
        ->and($party->status)->toBe(PartyStatus::Active);
});

test('party factory creates party for specific company', function () {
    $companyId = 'test-company-123';
    $party = Party::factory()->forCompany($companyId)->create();

    expect($party->company_id)->toBe($companyId);
});

test('party factory creates inactive party', function () {
    $party = Party::factory()->inactive()->create();

    expect($party->status)->toBe(PartyStatus::Inactive);
});

test('party factory creates active party', function () {
    $party = Party::factory()->active()->create();

    expect($party->status)->toBe(PartyStatus::Active);
});

test('party factory creates party without email', function () {
    $party = Party::factory()->withoutEmail()->create();

    expect($party->email)->toBeNull();
});

test('party factory creates party without address', function () {
    $party = Party::factory()->withoutAddress()->create();

    expect($party->address)->toBeNull();
});

test('party factory creates party with notes', function () {
    $party = Party::factory()->withNotes('Custom notes')->create();

    expect($party->notes)->toBe('Custom notes');
});

test('party factory creates minimal party', function () {
    $party = Party::factory()->minimal()->create();

    expect($party->email)->toBeNull()
        ->and($party->address)->toBeNull()
        ->and($party->notes)->toBeNull()
        ->and($party->name)->not->toBeNull()
        ->and($party->phone)->not->toBeNull();
});

test('party role factory creates valid role with default attributes', function () {
    $role = PartyRole::factory()->create();

    expect($role)->toBeInstanceOf(PartyRole::class)
        ->and($role->party_id)->not->toBeNull()
        ->and($role->role)->toBeInstanceOf(PartyRoleType::class);
});

test('party role factory creates role for specific party', function () {
    $party = Party::factory()->create();
    $role = PartyRole::factory()->forParty($party)->create();

    expect($role->party_id)->toBe($party->id);
});

test('party role factory creates supplier role', function () {
    $role = PartyRole::factory()->supplier()->create();

    expect($role->role)->toBe(PartyRoleType::Supplier);
});

test('party role factory creates farmer role', function () {
    $role = PartyRole::factory()->farmer()->create();

    expect($role->role)->toBe(PartyRoleType::Farmer);
});

test('party role factory creates owner role', function () {
    $role = PartyRole::factory()->owner()->create();

    expect($role->role)->toBe(PartyRoleType::Owner);
});

test('party role factory creates tenant role', function () {
    $role = PartyRole::factory()->tenant()->create();

    expect($role->role)->toBe(PartyRoleType::Tenant);
});

test('party role factory creates buyer role', function () {
    $role = PartyRole::factory()->buyer()->create();

    expect($role->role)->toBe(PartyRoleType::Buyer);
});

test('party role factory creates lessor role', function () {
    $role = PartyRole::factory()->lessor()->create();

    expect($role->role)->toBe(PartyRoleType::Lessor);
});

test('party role factory creates contractor role', function () {
    $role = PartyRole::factory()->contractor()->create();

    expect($role->role)->toBe(PartyRoleType::Contractor);
});

test('party role factory creates role with notes', function () {
    $role = PartyRole::factory()->withNotes('Custom role notes')->create();

    expect($role->notes)->toBe('Custom role notes');
});

test('party role factory creates role without notes', function () {
    $role = PartyRole::factory()->withoutNotes()->create();

    expect($role->notes)->toBeNull();
});

test('party can have multiple roles', function () {
    $party = Party::factory()->create();

    // Create roles with specific types to avoid duplicates
    PartyRole::factory()->forParty($party)->supplier()->create();
    PartyRole::factory()->forParty($party)->farmer()->create();
    PartyRole::factory()->forParty($party)->owner()->create();

    expect($party->roles()->count())->toBe(3);
});

test('test case helper creates party correctly', function () {
    $party = $this->createParty('test-company');

    expect($party)->toBeInstanceOf(Party::class)
        ->and($party->company_id)->toBe('test-company');
});

test('test case helper creates party role correctly', function () {
    $party = $this->createParty();
    $role = $this->createPartyRole($party);

    expect($role)->toBeInstanceOf(PartyRole::class)
        ->and($role->party_id)->toBe($party->id);
});
