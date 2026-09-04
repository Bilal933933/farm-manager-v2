<?php

use Illuminate\Support\Str;

test('rejects role on unknown party', function () {
    $response = $this->postJson('/api/parties/'.(string) Str::uuid().'/roles', ['role' => 'supplier'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(404);
});

test('rejects duplicate role for same party', function () {
    $party = createParty();
    createPartyRole($party, ['role' => 'supplier']);

    $response = $this->postJson('/api/parties/'.$party->id.'/roles', ['role' => 'supplier'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(422);
});

test('adds role to party', function () {
    $party = createParty();

    $response = $this->postJson('/api/parties/'.$party->id.'/roles', ['role' => 'buyer'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.role', 'buyer');
    $this->assertDatabaseHas('party_roles', ['party_id' => $party->id, 'role' => 'buyer']);
});

test('rejects role on another company party', function () {
    $party = createParty('comp-a');

    $response = $this->postJson('/api/parties/'.$party->id.'/roles', ['role' => 'buyer'], $this->serviceHeaders('comp-b', ['update_parties']));

    $response->assertStatus(403);
});

test('rejects invalid role value', function () {
    $party = createParty();

    $response = $this->postJson('/api/parties/'.$party->id.'/roles', ['role' => 'king'], $this->serviceHeaders('comp-a', ['update_parties']));

    $response->assertStatus(422);
});
