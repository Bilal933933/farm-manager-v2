<?php

use Illuminate\Support\Str;

test('creates season with existing crop', function () {
    $land = createLand();
    $crop = createCrop();

    $response = $this->postJson('/api/lands/'.$land->id.'/seasons', [
        'product_id' => $crop->id,
        'name' => 'Winter 2026',
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-a', ['create_seasons']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.land_id', $land->id);
});

test('rejects season with unknown product', function () {
    $land = createLand();

    $response = $this->postJson('/api/lands/'.$land->id.'/seasons', [
        'product_id' => (string) Str::uuid(),
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-a', ['create_seasons']));

    $response->assertStatus(422);
});

test('rejects season creation on another company land', function () {
    $land = createLand('comp-a');
    $crop = createCrop();

    $response = $this->postJson('/api/lands/'.$land->id.'/seasons', [
        'product_id' => $crop->id,
        'start_date' => '2026-01-01',
    ], $this->serviceHeaders('comp-b', ['create_seasons']));

    $response->assertStatus(403);
});

test('lists only seasons of the land', function () {
    $land = createLand();
    $otherLand = createLand('comp-a');
    createSeason($land);
    createSeason($land);
    createSeason($otherLand);

    $response = $this->getJson('/api/lands/'.$land->id.'/seasons', $this->serviceHeaders('comp-a'));

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
});

test('rejects showing season of another company', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);

    $response = $this->getJson('/api/seasons/'.$season->id, $this->serviceHeaders('comp-b'));

    $response->assertStatus(403);
});

test('updates and deletes own season', function () {
    $land = createLand();
    $season = createSeason($land);

    $update = $this->putJson('/api/seasons/'.$season->id, ['name' => 'Updated'], $this->serviceHeaders('comp-a', ['update_seasons']));

    $update->assertStatus(200);

    $delete = $this->deleteJson('/api/seasons/'.$season->id, [], $this->serviceHeaders('comp-a', ['delete_seasons']));

    $delete->assertStatus(204);
});
