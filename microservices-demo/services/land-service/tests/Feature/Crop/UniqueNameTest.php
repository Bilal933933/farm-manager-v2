<?php

test('unique name rejects without token', function () {
    $this->postJson('/api/crops', ['name' => 'Guava'])->assertStatus(401);
});

test('unique name rejects without permission', function () {
    $this->postJson('/api/crops', ['name' => 'Guava'], $this->serviceHeaders('comp-a', ['view_crops']))->assertStatus(403);
});

test('unique name creates new crop', function () {
    $response = $this->postJson('/api/crops', ['name' => 'Guava', 'unit' => 'kg'], $this->serviceHeaders('comp-a', ['create_crops']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('crops', ['name' => 'Guava']);
});

test('unique name rejects duplicate', function () {
    createCrop(['name' => 'Kiwi']);

    $this->postJson('/api/crops', ['name' => 'Kiwi'], $this->serviceHeaders('comp-a', ['create_crops']))->assertStatus(422);
});

test('unique name rejects update to existing name', function () {
    createCrop(['name' => 'Fig']);
    $crop = createCrop(['name' => 'Plum']);

    $this->putJson('/api/crops/'.$crop->id, ['name' => 'Fig'], $this->serviceHeaders('comp-a', ['update_crops']))->assertStatus(422);
});

test('unique name allows keeping same name on update', function () {
    $crop = createCrop(['name' => 'Peach']);

    $this->putJson('/api/crops/'.$crop->id, ['name' => 'Peach', 'description' => 'Sweet'], $this->serviceHeaders('comp-a', ['update_crops']))->assertStatus(200);
});
