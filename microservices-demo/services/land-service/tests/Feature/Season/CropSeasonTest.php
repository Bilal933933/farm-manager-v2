<?php

use Illuminate\Support\Str;

test('crop season rejects without token', function () {
    $land = createLand();
    $crop = createCrop();

    $this->postJson('/api/lands/'.$land->id.'/seasons', ['product_id' => $crop->id, 'start_date' => '2026-01-01'])->assertStatus(401);
});

test('crop season rejects without permission', function () {
    $land = createLand();
    $crop = createCrop();

    $this->postJson('/api/lands/'.$land->id.'/seasons', ['product_id' => $crop->id, 'start_date' => '2026-01-01'], $this->serviceHeaders('comp-a', ['view_seasons']))->assertStatus(403);
});

test('crop season rejects on another company land', function () {
    $land = createLand('comp-a');
    $crop = createCrop();

    $this->postJson('/api/lands/'.$land->id.'/seasons', ['product_id' => $crop->id, 'start_date' => '2026-01-01'], $this->serviceHeaders('comp-b', ['create_seasons']))->assertStatus(403);
});

test('crop season creates with existing crop', function () {
    $land = createLand();
    $crop = createCrop();

    $response = $this->postJson('/api/lands/'.$land->id.'/seasons', ['product_id' => $crop->id, 'start_date' => '2026-01-01'], $this->serviceHeaders('comp-a', ['create_seasons']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('seasons', ['land_id' => $land->id, 'product_id' => $crop->id]);
});

test('crop season rejects unknown crop', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/seasons', ['product_id' => (string) Str::uuid(), 'start_date' => '2026-01-01'], $this->serviceHeaders('comp-a', ['create_seasons']))->assertStatus(422);
});

test('crop season rejects missing product', function () {
    $land = createLand();

    $this->postJson('/api/lands/'.$land->id.'/seasons', ['start_date' => '2026-01-01'], $this->serviceHeaders('comp-a', ['create_seasons']))->assertStatus(422);
});
