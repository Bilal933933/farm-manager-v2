<?php

use App\Enums\PartyRoleType;
use App\Enums\PartyStatus;
use App\Models\Party;
use App\Models\PartyRole;
use Database\Seeders\PartySeeder;

test('party seeder creates parties for company 1', function () {
    $this->seed(PartySeeder::class);

    $parties = Party::where('company_id', 'company-1')->get();

    expect($parties->count())->toBeGreaterThan(0);
});

test('party seeder creates parties for company 2', function () {
    $this->seed(PartySeeder::class);

    $parties = Party::where('company_id', 'company-2')->get();

    expect($parties->count())->toBeGreaterThan(0);
});

test('party seeder creates active and inactive parties', function () {
    $this->seed(PartySeeder::class);

    $activeParties = Party::where('status', PartyStatus::Active)->count();
    $inactiveParties = Party::where('status', PartyStatus::Inactive)->count();

    expect($activeParties)->toBeGreaterThan(0)
        ->and($inactiveParties)->toBeGreaterThan(0);
});

test('party seeder creates specific named parties', function () {
    $this->seed(PartySeeder::class);

    $supplier = Party::where('name', 'ABC Agricultural Supplies')->first();
    $farmer = Party::where('name', 'Ahmed Farm')->first();

    expect($supplier)->not->toBeNull()
        ->and($farmer)->not->toBeNull();
});

test('party seeder creates parties with roles', function () {
    $this->seed(PartySeeder::class);

    $partiesWithRoles = Party::has('roles')->count();

    expect($partiesWithRoles)->toBeGreaterThan(0);
});

test('party seeder creates specific role types', function () {
    $this->seed(PartySeeder::class);

    $supplierRoles = PartyRole::where('role', PartyRoleType::Supplier)->count();
    $farmerRoles = PartyRole::where('role', PartyRoleType::Farmer)->count();

    expect($supplierRoles)->toBeGreaterThan(0)
        ->and($farmerRoles)->toBeGreaterThan(0);
});

test('database seeder runs party seeder', function () {
    $this->seed();

    $parties = Party::count();

    expect($parties)->toBeGreaterThan(0);
});
