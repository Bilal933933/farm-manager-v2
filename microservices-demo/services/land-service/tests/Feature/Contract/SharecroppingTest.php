<?php

use Illuminate\Support\Str;

function sharecroppingPayload(): array
{
    return [
        'contract_type' => 'sharecropping',
        'counterparty_party_id' => (string) Str::uuid(),
        'revenue_share_percentage' => 30,
        'start_date' => '2026-01-01',
    ];
}

test('sharecropping rejects without token', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/contracts', sharecroppingPayload())->assertStatus(401);
});

test('sharecropping rejects without permission', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/contracts', sharecroppingPayload(), $this->serviceHeaders('comp-a', ['view_contracts']))->assertStatus(403);
});

test('sharecropping rejects on another company land', function () {
    $land = createLand('comp-a');

    $this->postJson('/api/lands/'.$land->id.'/contracts', sharecroppingPayload(), $this->serviceHeaders('comp-b', ['create_contracts']))->assertStatus(403);
});

test('sharecropping creates with revenue share', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/contracts', sharecroppingPayload(), $this->serviceHeaders('comp-a', ['create_contracts']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.revenue_share_percentage', '30.00');
    $this->assertDatabaseHas('contracts', ['land_id' => $land->id, 'revenue_share_percentage' => 30]);
});

test('sharecropping rejects without revenue share', function () {
    $land = createLand();
    $payload = sharecroppingPayload();
    unset($payload['revenue_share_percentage']);

    $this->postJson('/api/lands/'.$land->id.'/contracts', $payload, $this->serviceHeaders('comp-a', ['create_contracts']))->assertStatus(422);
});

test('sharecropping rejects share above 100', function () {
    $land = createLand();
    $payload = sharecroppingPayload();
    $payload['revenue_share_percentage'] = 150;

    $this->postJson('/api/lands/'.$land->id.'/contracts', $payload, $this->serviceHeaders('comp-a', ['create_contracts']))->assertStatus(422);
});
