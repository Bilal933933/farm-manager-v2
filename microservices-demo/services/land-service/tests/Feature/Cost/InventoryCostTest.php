<?php

use Illuminate\Support\Str;

function inventoryCostPayload(): array
{
    return [
        'cost_type' => 'seeds',
        'product_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit_price' => 12,
        'date' => '2026-02-01',
    ];
}

test('inventory cost rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/costs', inventoryCostPayload())->assertStatus(401);
});

test('inventory cost rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/costs', inventoryCostPayload(), $this->serviceHeaders('comp-a', ['view_costs']))->assertStatus(403);
});

test('inventory cost rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/costs', inventoryCostPayload(), $this->serviceHeaders('comp-b', ['create_costs']))->assertStatus(403);
});

test('inventory cost computes amount', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', inventoryCostPayload(), $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.amount', '120.00');
    $this->assertDatabaseHas('costs', ['season_id' => $season->id, 'amount' => 120]);
});

test('inventory cost rejects missing quantity with product', function () {
    $season = createSeason(createLand());
    $payload = inventoryCostPayload();
    unset($payload['quantity']);

    $this->postJson('/api/seasons/'.$season->id.'/costs', $payload, $this->serviceHeaders('comp-a', ['create_costs']))->assertStatus(422);
});
