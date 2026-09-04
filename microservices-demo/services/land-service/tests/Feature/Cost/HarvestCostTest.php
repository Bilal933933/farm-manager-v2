<?php

use Illuminate\Support\Str;

function harvestCostPayload(string $harvestId): array
{
    return [
        'cost_type' => 'transport',
        'amount' => 300,
        'date' => '2026-03-05',
        'harvest_id' => $harvestId,
    ];
}

test('harvest cost rejects without token', function () {
    $season = createSeason(createLand());
    $harvest = createHarvest($season);

    $this->postJson('/api/seasons/'.$season->id.'/costs', harvestCostPayload($harvest->id))->assertStatus(401);
});

test('harvest cost rejects without permission', function () {
    $season = createSeason(createLand());
    $harvest = createHarvest($season);

    $this->postJson('/api/seasons/'.$season->id.'/costs', harvestCostPayload($harvest->id), $this->serviceHeaders('comp-a', ['view_costs']))->assertStatus(403);
});

test('harvest cost links same season harvest', function () {
    $season = createSeason(createLand());
    $harvest = createHarvest($season);

    $response = $this->postJson('/api/seasons/'.$season->id.'/costs', harvestCostPayload($harvest->id), $this->serviceHeaders('comp-a', ['create_costs']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.harvest_id', $harvest->id);
    $this->assertDatabaseHas('costs', ['season_id' => $season->id, 'harvest_id' => $harvest->id]);
});

test('harvest cost rejects harvest from another season', function () {
    $land = createLand();
    $season = createSeason($land);
    $otherHarvest = createHarvest(createSeason($land));

    $this->postJson('/api/seasons/'.$season->id.'/costs', harvestCostPayload($otherHarvest->id), $this->serviceHeaders('comp-a', ['create_costs']))->assertStatus(422);
});

test('harvest cost rejects unknown harvest', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/costs', harvestCostPayload((string) Str::uuid()), $this->serviceHeaders('comp-a', ['create_costs']))->assertStatus(422);
});
