<?php

use Illuminate\Support\Str;

function validLandPayload(): array
{
    return [
        'slug' => 'test-land-'.Str::random(8),
        'name' => 'Test Land',
        'area' => 10.5,
        'ownership_type' => 'owned',
        'owner_party_id' => (string) Str::uuid(),
    ];
}

test('rejects land creation without create_lands permission', function () {
    $response = $this->postJson('/api/lands', validLandPayload(), $this->serviceHeaders('comp-1', ['view_lands']));

    $response->assertStatus(403);
});

test('rejects land creation with invalid data', function () {
    $response = $this->postJson('/api/lands', [], $this->serviceHeaders('comp-1', ['create_lands']));

    $response->assertStatus(422);
});

test('creates land with company_id from headers', function () {
    $response = $this->postJson('/api/lands', validLandPayload(), $this->serviceHeaders('comp-1', ['create_lands']));

    $response->assertStatus(201);

    $this->assertDatabaseHas('lands', ['company_id' => 'comp-1']);
});
