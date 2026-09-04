<?php

test('phone unique is scoped to company', function () {
    createParty('comp-a', ['name' => 'A One', 'phone' => '01077777777']);

    $response = $this->postJson('/api/parties', ['name' => 'B One', 'phone' => '01077777777'], $this->serviceHeaders('comp-b', ['create_parties']));

    $response->assertStatus(201);
});

test('update keeps own name', function () {
    $party = createParty('comp-a', ['name' => 'Keep Me']);

    $response = $this->putJson('/api/parties/'.$party->id, ['name' => 'Keep Me', 'notes' => 'Updated'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(200);
});

test('update rejects name taken by another party', function () {
    createParty('comp-a', ['name' => 'Taken', 'phone' => '01088888888']);
    $party = createParty('comp-a', ['name' => 'Free', 'phone' => '01099999999']);

    $response = $this->putJson('/api/parties/'.$party->id, ['name' => 'Taken'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(422);
});

test('rejects invalid email', function () {
    $response = $this->postJson('/api/parties', ['name' => 'Bad Mail', 'phone' => '01000000000', 'email' => 'not-an-email'], $this->serviceHeaders('comp-a', ['create_parties']));

    $response->assertStatus(422);
});
