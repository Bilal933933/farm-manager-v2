<?php

use Illuminate\Support\Str;

function rentInPayload(): array
{
    return [
        'contract_type' => 'rent_in',
        'counterparty_party_id' => (string) Str::uuid(),
        'financial_value' => 5000,
        'start_date' => '2026-01-01',
    ];
}

test('rent_in rejects without token', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/contracts', rentInPayload())->assertStatus(401);
});

test('rent_in rejects without permission', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/contracts', rentInPayload(), $this->serviceHeaders('comp-a', ['view_contracts']))->assertStatus(403);
});

test('rent_in rejects on another company land', function () {
    $land = createLand('comp-a');

    $this->postJson('/api/lands/'.$land->id.'/contracts', rentInPayload(), $this->serviceHeaders('comp-b', ['create_contracts']))->assertStatus(403);
});

test('rent_in creates with financial value', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', rentInPayload(), $this->serviceHeaders('comp-a', ['create_contracts']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.contract_type', 'rent_in');
    $this->assertDatabaseHas('contracts', ['land_id' => $land->id, 'financial_value' => 5000]);
});

test('rent_in rejects without financial value', function () {
    $land = createLand();
    $payload = rentInPayload();
    unset($payload['financial_value']);

    $this->postJson('/api/lands/'.$land->id.'/contracts', $payload, $this->serviceHeaders('comp-a', ['create_contracts']))->assertStatus(422);
});
