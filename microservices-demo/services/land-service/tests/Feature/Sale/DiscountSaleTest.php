<?php

use Illuminate\Support\Str;

function discountSalePayload(): array
{
    return [
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit' => 'kg',
        'unit_price' => 100,
        'discount_amount' => 100,
        'tax_amount' => 50,
        'delivery_cost' => 25,
        'date' => '2026-04-01',
    ];
}

test('discount sale rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', discountSalePayload())->assertStatus(401);
});

test('discount sale rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', discountSalePayload(), $this->serviceHeaders('comp-a', ['view_sales']))->assertStatus(403);
});

test('discount sale computes total with tax and delivery', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', discountSalePayload(), $this->serviceHeaders('comp-a', ['create_sales']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.total_price', '975.00');
    $this->assertDatabaseHas('sales', ['season_id' => $season->id, 'total_price' => 975]);
});

test('discount sale rejects negative discount', function () {
    $season = createSeason(createLand());
    $payload = discountSalePayload();
    $payload['discount_amount'] = -5;

    $this->postJson('/api/seasons/'.$season->id.'/sales', $payload, $this->serviceHeaders('comp-a', ['create_sales']))->assertStatus(422);
});

test('discount sale rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/sales', discountSalePayload(), $this->serviceHeaders('comp-b', ['create_sales']))->assertStatus(403);
});
