<?php

use Illuminate\Support\Str;

test('computes total price with discount', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', [
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 100,
        'unit' => 'kg',
        'unit_price' => 10,
        'discount_amount' => 50,
        'date' => '2026-04-01',
    ], $this->serviceHeaders('comp-a', ['create_sales']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.total_price', '950.00');
});

test('rejects sale without buyer', function () {
    $land = createLand();
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', [
        'quantity' => 10,
        'unit' => 'kg',
        'unit_price' => 5,
        'date' => '2026-04-02',
    ], $this->serviceHeaders('comp-a', ['create_sales']));

    $response->assertStatus(422);
});

test('rejects sale creation on another company season', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', [
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit' => 'kg',
        'unit_price' => 5,
        'date' => '2026-04-02',
    ], $this->serviceHeaders('comp-b', ['create_sales']));

    $response->assertStatus(403);
});

test('rejects showing sale of another company', function () {
    $land = createLand('comp-a');
    $season = createSeason($land);
    $sale = createSale($season);

    $response = $this->getJson('/api/sales/'.$sale->id, $this->serviceHeaders('comp-b'));

    $response->assertStatus(403);
});

test('recomputes total on update', function () {
    $land = createLand();
    $season = createSeason($land);
    $sale = createSale($season, ['quantity' => 100, 'unit_price' => 10, 'discount_amount' => 50, 'total_price' => 950]);

    $response = $this->putJson('/api/sales/'.$sale->id, ['tax_amount' => 100], $this->serviceHeaders('comp-a', ['update_sales']));

    $response->assertStatus(200);
    $response->assertJsonPath('data.total_price', '1050.00');
});

test('deletes own sale', function () {
    $land = createLand();
    $season = createSeason($land);
    $sale = createSale($season);

    $response = $this->deleteJson('/api/sales/'.$sale->id, [], $this->serviceHeaders('comp-a', ['delete_sales']));

    $response->assertStatus(204);
});
