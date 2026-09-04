<?php

function operationalCostPayload(): array
{
    return [
        'cost_type' => 'labor',
        'amount' => 5000,
        'date' => '2026-02-02',
    ];
}

test('operational cost rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/costs', operationalCostPayload())->assertStatus(401);
});

test('operational cost rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/costs', operationalCostPayload(), $this->serviceHeaders('comp-a', ['view_costs']))->assertStatus(403);
});

test('operational cost rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/costs', operationalCostPayload(), $this->serviceHeaders('comp-b', ['create_costs']))->assertStatus(403);
});

test('operational cost creates without product', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', operationalCostPayload(), $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.amount', '5000.00');
    $this->assertDatabaseHas('costs', ['season_id' => $season->id, 'cost_type' => 'labor']);
});

test('operational cost rejects missing amount without product', function () {
    $season = createSeason(createLand());
    $payload = operationalCostPayload();
    unset($payload['amount']);

    $this->postJson('/api/seasons/'.$season->id.'/costs', $payload, $this->serviceHeaders('comp-a', ['create_costs']))->assertStatus(422);
});
