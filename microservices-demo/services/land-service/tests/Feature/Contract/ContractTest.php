<?php

use Illuminate\Support\Str;

test('creates sharecropping contract with revenue share', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', [
        'contract_type' => 'sharecropping',
        'counterparty_party_id' => (string) Str::uuid(),
        'revenue_share_percentage' => 30,
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-a', ['create_contracts']));

    $response->assertStatus(201);
});

test('rejects rent contract without financial value', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', [
        'contract_type' => 'rent_in',
        'counterparty_party_id' => (string) Str::uuid(),
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-a', ['create_contracts']));

    $response->assertStatus(422);
});

test('creates rent contract with financial value', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', [
        'contract_type' => 'rent_in',
        'counterparty_party_id' => (string) Str::uuid(),
        'financial_value' => 5000,
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-a', ['create_contracts']));

    $response->assertStatus(201);
});

test('rejects contract creation on another company land', function () {
    $land = createLand('comp-a');

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', [
        'contract_type' => 'sharecropping',
        'counterparty_party_id' => (string) Str::uuid(),
        'revenue_share_percentage' => 30,
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-b', ['create_contracts']));

    $response->assertStatus(403);
});

test('updates and deletes own contract', function () {
    $land = createLand();
    $contract = createContract($land);

    $update = $this->putJson('/api/contracts/'.$contract->id, ['notes' => 'Renewed'], $this->serviceHeaders('comp-a', ['update_contracts']));

    $update->assertStatus(200);

    $delete = $this->deleteJson('/api/contracts/'.$contract->id, [], $this->serviceHeaders('comp-a', ['delete_contracts']));

    $delete->assertStatus(204);
});
