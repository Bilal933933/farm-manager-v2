<?php

function ownedPayload(string $type): array
{
    $payload = validLandPayload();
    $payload['ownership_type'] = $type;

    return $payload;
}

test('land rejects without token', function () {
    $this->postJson('/api/lands', validLandPayload())->assertStatus(401);
});

test('land rejects without permission', function () {
    $this->postJson('/api/lands', validLandPayload(), $this->serviceHeaders('comp-a', ['view_lands']))->assertStatus(403);
});

test('land creates owned type', function () {
    $response = $this->postJson('/api/lands', ownedPayload('owned'), $this->serviceHeaders('comp-a', ['create_lands']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.ownership_type', 'owned');
    $this->assertDatabaseHas('lands', ['company_id' => 'comp-a', 'ownership_type' => 'owned']);
});

test('land creates rented_in type', function () {
    $response = $this->postJson('/api/lands', ownedPayload('rented_in'), $this->serviceHeaders('comp-a', ['create_lands']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.ownership_type', 'rented_in');
});

test('land creates shared type', function () {
    $response = $this->postJson('/api/lands', ownedPayload('shared'), $this->serviceHeaders('comp-a', ['create_lands']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.ownership_type', 'shared');
});

test('land rejects invalid ownership type', function () {
    $this->postJson('/api/lands', ownedPayload('rented'), $this->serviceHeaders('comp-a', ['create_lands']))->assertStatus(422);
});
