<?php

test('shows own land', function () {
    $land = createLand();

    $response = $this->getJson('/api/lands/'.$land->id, $this->serviceHeaders('comp-a'));

    $response->assertStatus(200);
    $response->assertJsonPath('data.id', $land->id);
});

test('rejects showing land of another company', function () {
    $land = createLand('comp-a');

    $response = $this->getJson('/api/lands/'.$land->id, $this->serviceHeaders('comp-b'));

    $response->assertStatus(403);
});

test('updates own land', function () {
    $land = createLand();

    $response = $this->putJson('/api/lands/'.$land->id, ['name' => 'Renamed Land'], $this->serviceHeaders('comp-a', ['update_lands']));

    $response->assertStatus(200);
    $this->assertDatabaseHas('lands', ['id' => $land->id, 'name' => 'Renamed Land']);
});

test('rejects updating land of another company', function () {
    $land = createLand('comp-a');

    $response = $this->putJson('/api/lands/'.$land->id, ['name' => 'Hacked'], $this->serviceHeaders('comp-b', ['update_lands']));

    $response->assertStatus(403);
});

test('rejects updating land without permission', function () {
    $land = createLand();

    $response = $this->putJson('/api/lands/'.$land->id, ['name' => 'Hacked'], $this->serviceHeaders('comp-a', ['view_lands']));

    $response->assertStatus(403);
});

test('rejects deleting land without permission', function () {
    $land = createLand();

    $response = $this->deleteJson('/api/lands/'.$land->id, [], $this->serviceHeaders('comp-a', ['view_lands']));

    $response->assertStatus(403);
});

test('deletes own land and hides it afterwards', function () {
    $land = createLand();

    $delete = $this->deleteJson('/api/lands/'.$land->id, [], $this->serviceHeaders('comp-a', ['delete_lands']));

    $delete->assertStatus(204);
    $this->assertSoftDeleted('lands', ['id' => $land->id]);

    $show = $this->getJson('/api/lands/'.$land->id, $this->serviceHeaders('comp-a'));

    $show->assertStatus(404);
});
