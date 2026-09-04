<?php

function harvestPayload(string $unit): array
{
    return [
        'total_quantity' => 100,
        'unit' => $unit,
        'date' => '2026-03-01',
    ];
}

test('harvest rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('kg'))->assertStatus(401);
});

test('harvest rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('kg'), $this->serviceHeaders('comp-a', ['view_harvests']))->assertStatus(403);
});

test('harvest rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('kg'), $this->serviceHeaders('comp-b', ['create_harvests']))->assertStatus(403);
});

test('harvest creates with kg unit', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('kg'), $this->serviceHeaders('comp-a', ['create_harvests']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.unit', 'kg');
    $this->assertDatabaseHas('harvests', ['season_id' => $season->id, 'unit' => 'kg']);
});

test('harvest creates with ton unit', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('ton'), $this->serviceHeaders('comp-a', ['create_harvests']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.unit', 'ton');
});

test('harvest creates with sack unit', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', harvestPayload('sack'), $this->serviceHeaders('comp-a', ['create_harvests']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.unit', 'sack');
});

test('harvest rejects missing unit', function () {
    $season = createSeason(createLand());
    $payload = harvestPayload('kg');
    unset($payload['unit']);

    $this->postJson('/api/seasons/'.$season->id.'/harvests', $payload, $this->serviceHeaders('comp-a', ['create_harvests']))->assertStatus(422);
});
