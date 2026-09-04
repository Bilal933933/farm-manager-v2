<?php

test('creates crop', function () {
    $response = $this->postJson('/api/crops', ['name' => 'Mango', 'unit' => 'kg'], $this->serviceHeaders('comp-a', ['create_crops']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.name', 'Mango');
});

test('rejects duplicate crop name', function () {
    createCrop(['name' => 'MangoDup']);

    $response = $this->postJson('/api/crops', ['name' => 'MangoDup'], $this->serviceHeaders('comp-a', ['create_crops']));

    $response->assertStatus(422);
});

test('rejects crop creation without permission', function () {
    $response = $this->postJson('/api/crops', ['name' => 'Papaya'], $this->serviceHeaders('comp-a', ['view_crops']));

    $response->assertStatus(403);
});

test('updates and deletes crop', function () {
    $crop = createCrop();

    $update = $this->putJson('/api/crops/'.$crop->id, ['description' => 'Tropical'], $this->serviceHeaders('comp-a', ['update_crops']));

    $update->assertStatus(200);

    $delete = $this->deleteJson('/api/crops/'.$crop->id, [], $this->serviceHeaders('comp-a', ['delete_crops']));

    $delete->assertStatus(204);
});
