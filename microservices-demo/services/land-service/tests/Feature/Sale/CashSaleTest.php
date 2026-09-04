<?php

use Illuminate\Support\Str;

function cashSalePayload(): array
{
    return [
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 50,
        'unit' => 'kg',
        'unit_price' => 20,
        'payment_method' => 'cash',
        'payment_status' => 'paid',
        'date' => '2026-04-01',
    ];
}

test('cash sale rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', cashSalePayload())->assertStatus(401);
});

test('cash sale rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', cashSalePayload(), $this->serviceHeaders('comp-a', ['view_sales']))->assertStatus(403);
});

test('cash sale rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/sales', cashSalePayload(), $this->serviceHeaders('comp-b', ['create_sales']))->assertStatus(403);
});

test('cash sale creates paid total', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', cashSalePayload(), $this->serviceHeaders('comp-a', ['create_sales']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.total_price', '1000.00');
    $response->assertJsonPath('data.payment_status', 'paid');
    $this->assertDatabaseHas('sales', ['season_id' => $season->id, 'total_price' => 1000]);
});

test('cash sale rejects without buyer', function () {
    $season = createSeason(createLand());
    $payload = cashSalePayload();
    unset($payload['buyer_party_id']);

    $this->postJson('/api/seasons/'.$season->id.'/sales', $payload, $this->serviceHeaders('comp-a', ['create_sales']))->assertStatus(422);
});
