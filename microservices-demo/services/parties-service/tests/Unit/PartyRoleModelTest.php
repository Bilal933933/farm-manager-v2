<?php

use App\Enums\PartyRoleType;
use App\Models\Party;
use App\Models\PartyRole;

beforeEach(function () {
    $this->party = Party::factory()->create();
});

// Soft Deletes Tests
test('party role can be soft deleted', function () {
    $role = PartyRole::factory()->forParty($this->party)->create();

    $role->delete();

    expect($role->trashed())->toBeTrue()
        ->and(PartyRole::withTrashed()->find($role->id))->not->toBeNull()
        ->and(PartyRole::find($role->id))->toBeNull();
});

test('soft deleted party role can be restored', function () {
    $role = PartyRole::factory()->forParty($this->party)->create();

    $role->delete();
    $role->restore();

    expect($role->trashed())->toBeFalse()
        ->and(PartyRole::find($role->id))->not->toBeNull();
});

test('party role can be force deleted', function () {
    $role = PartyRole::factory()->forParty($this->party)->create();
    $roleId = $role->id;

    $role->forceDelete();

    expect(PartyRole::withTrashed()->find($roleId))->toBeNull();
});

// Scope Tests
test('scope filters roles by type', function () {
    PartyRole::factory()->forParty($this->party)->supplier()->create();
    PartyRole::factory()->forParty($this->party)->farmer()->create();

    $suppliers = PartyRole::ofType(PartyRoleType::Supplier)->get();

    expect($suppliers)->toHaveCount(1)
        ->and($suppliers->first()->role)->toBe(PartyRoleType::Supplier);
});

test('scope suppliers returns only supplier roles', function () {
    PartyRole::factory()->forParty($this->party)->supplier()->create();
    PartyRole::factory()->forParty($this->party)->farmer()->create();

    $suppliers = PartyRole::suppliers()->get();

    expect($suppliers)->toHaveCount(1)
        ->and($suppliers->first()->role)->toBe(PartyRoleType::Supplier);
});

test('scope farmers returns only farmer roles', function () {
    PartyRole::factory()->forParty($this->party)->supplier()->create();
    PartyRole::factory()->forParty($this->party)->farmer()->create();

    $farmers = PartyRole::farmers()->get();

    expect($farmers)->toHaveCount(1)
        ->and($farmers->first()->role)->toBe(PartyRoleType::Farmer);
});

test('scope owners returns only owner roles', function () {
    PartyRole::factory()->forParty($this->party)->owner()->create();
    PartyRole::factory()->forParty($this->party)->farmer()->create();

    $owners = PartyRole::owners()->get();

    expect($owners)->toHaveCount(1)
        ->and($owners->first()->role)->toBe(PartyRoleType::Owner);
});

// Helper Method Tests
test('isType returns true for matching role type', function () {
    $role = PartyRole::factory()->forParty($this->party)->supplier()->create();

    expect($role->isType(PartyRoleType::Supplier))->toBeTrue()
        ->and($role->isType(PartyRoleType::Farmer))->toBeFalse();
});

test('isSupplier returns true for supplier role', function () {
    $role = PartyRole::factory()->forParty($this->party)->supplier()->create();

    expect($role->isSupplier())->toBeTrue();
});

test('isFarmer returns true for farmer role', function () {
    $role = PartyRole::factory()->forParty($this->party)->farmer()->create();

    expect($role->isFarmer())->toBeTrue();
});

test('isOwner returns true for owner role', function () {
    $role = PartyRole::factory()->forParty($this->party)->owner()->create();

    expect($role->isOwner())->toBeTrue();
});

test('isBuyer returns true for buyer role', function () {
    $role = PartyRole::factory()->forParty($this->party)->buyer()->create();

    expect($role->isBuyer())->toBeTrue();
});

// Relationship Tests
test('party role belongs to party', function () {
    $role = PartyRole::factory()->forParty($this->party)->create();

    expect($role->party)->toBeInstanceOf(Party::class)
        ->and($role->party->id)->toBe($this->party->id);
});
