<?php

test('rejects party creation without permission', function () {
    $response = $this->postJson('/api/parties', ['name' => 'Ahmed', 'phone' => '01011111111'], $this->serviceHeaders('comp-a', ['view_parties']));

    $response->assertStatus(403);
});

test('rejects duplicate name within same company', function () {
    createParty('comp-a', ['name' => 'Same Name', 'phone' => '01011111111']);

    $response = $this->postJson('/api/parties', ['name' => 'Same Name', 'phone' => '01022222222'], $this->serviceHeaders('comp-a', ['create_parties']));

    $response->assertStatus(422);
});

test('rejects duplicate phone within same company', function () {
    createParty('comp-a', ['name' => 'First', 'phone' => '01033333333']);

    $response = $this->postJson('/api/parties', ['name' => 'Second', 'phone' => '01033333333'], $this->serviceHeaders('comp-a', ['create_parties']));

    $response->assertStatus(422);
});

test('creates party with company from headers', function () {
    $response = $this->postJson('/api/parties', ['name' => 'New Party', 'phone' => '01044444444'], $this->serviceHeaders('comp-a', ['create_parties']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('parties', ['company_id' => 'comp-a', 'name' => 'New Party']);
});

test('allows same name in another company', function () {
    createParty('comp-a', ['name' => 'Shared Name', 'phone' => '01055555555']);

    $response = $this->postJson('/api/parties', ['name' => 'Shared Name', 'phone' => '01066666666'], $this->serviceHeaders('comp-b', ['create_parties']));

    $response->assertStatus(201);
});
