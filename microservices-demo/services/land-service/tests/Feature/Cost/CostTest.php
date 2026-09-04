<?php

use Illuminate\Support\Str;

test('computes amount from quantity and unit price', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', [
        'cost_type' => 'seeds',
        'product_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit_price' => 12,
        'date' => '2026-02-01',
    ], $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.amount', '120.00');
});

test('creates labor cost without product', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', [
        'cost_type' => 'labor',
        'amount' => 5000,
        'date' => '2026-02-02',
    ], $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
});

test('uses mock price when unit price missing', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', [
        'cost_type' => 'fertilizer',
        'product_id' => (string) Str::uuid(),
        'quantity' => 5,
        'date' => '2026-02-03',
    ], $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.unit_price', '10.00');
    $response->assertJsonPath('data.amount', '50.00');
});

test('rejects harvest from another season', function () {
    $land = createLand();
    $season = createSeason($land);
    $otherSeason = createSeason($land);
    $harvest = createHarvest($otherSeason);

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', [
        'cost_type' => 'labor',
        'amount' => 500,
        'date' => '2026-02-04',
        'harvest_id' => $harvest->id,
    ], $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(422);
});

test('rejects showing cost of another company', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);
    $cost = createCost($season);

    $response = $this->getJson('/api/costs/'.$cost->id, $this->serviceHeaders('comp-b'));

    $response->assertStatus(403);
});

test('deletes own cost', function () {
    $land = createLand();
    $season = createSeason($land);
    $cost = createCost($season);

    $response = $this->deleteJson('/api/costs/'.$cost->id, [], $this->serviceHeaders('comp-a', ['delete_costs']));

    $response->assertStatus(204);
});
