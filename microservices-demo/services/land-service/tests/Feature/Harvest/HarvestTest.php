<?php

test('creates harvest', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', [
        'total_quantity' => 100,
        'unit' => 'kg',
        'date' => '2026-03-01',
    ], $this->serviceHeaders('comp-a', ['create_harvests']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.season_id', $season->id);
});

test('rejects harvest creation on another company season', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', [
        'total_quantity' => 100,
        'unit' => 'kg',
        'date' => '2026-03-01',
    ], $this->serviceHeaders('comp-b', ['create_harvests']));

    $response->assertStatus(403);
});

test('rejects harvest with future date', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/harvests', [
        'total_quantity' => 100,
        'unit' => 'kg',
        'date' => '2999-01-01',
    ], $this->serviceHeaders('comp-a', ['create_harvests']));

    $response->assertStatus(422);
});

test('rejects showing harvest of another company', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);
    $harvest = createHarvest($season);

    $response = $this->getJson('/api/harvests/'.$harvest->id, $this->serviceHeaders('comp-b'));

    $response->assertStatus(403);
});

test('updates and deletes own harvest', function () {
    $land = createLand();
    $season = createSeason($land);
    $harvest = createHarvest($season);

    $update = $this->putJson('/api/harvests/'.$harvest->id, ['total_quantity' => 150], $this->serviceHeaders('comp-a', ['update_harvests']));

    $update->assertStatus(200);

    $delete = $this->deleteJson('/api/harvests/'.$harvest->id, [], $this->serviceHeaders('comp-a', ['delete_harvests']));

    $delete->assertStatus(204);
});
