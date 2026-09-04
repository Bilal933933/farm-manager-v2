<?php

use Illuminate\Support\Str;

function installmentSalePayload(): array
{
    return [
        'buyer_party_id' => (string) Str::uuid(),
        'quantity' => 10,
        'unit' => 'kg',
        'unit_price' => 100,
        'payment_method' => 'installment',
        'payment_status' => 'partially_paid',
        'date' => '2026-04-01',
        'due_date' => '2026-06-01',
    ];
}

test('installment sale rejects without token', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', installmentSalePayload())->assertStatus(401);
});

test('installment sale rejects without permission', function () {
    $season = createSeason(createLand());

    $this->postJson('/api/seasons/'.$season->id.'/sales', installmentSalePayload(), $this->serviceHeaders('comp-a', ['view_sales']))->assertStatus(403);
});

test('installment sale rejects on another company season', function () {
    $season = createSeason(createLand('comp-a'));

    $this->postJson('/api/seasons/'.$season->id.'/sales', installmentSalePayload(), $this->serviceHeaders('comp-b', ['create_sales']))->assertStatus(403);
});

test('installment sale creates with due date', function () {
    $season = createSeason(createLand());

    $response = $this->postJson('/api/seasons/'.$season->id.'/sales', installmentSalePayload(), $this->serviceHeaders('comp-a', ['create_sales']));

    $response->assertStatus(201);
    $response->assertJsonPath('data.payment_method', 'installment');
    $response->assertJsonPath('data.payment_status', 'partially_paid');
    $this->assertDatabaseHas('sales', ['season_id' => $season->id, 'payment_method' => 'installment']);
});

test('installment sale rejects due date before sale date', function () {
    $season = createSeason(createLand());
    $payload = installmentSalePayload();
    $payload['due_date'] = '2026-01-01';

    $this->postJson('/api/seasons/'.$season->id.'/sales', $payload, $this->serviceHeaders('comp-a', ['create_sales']))->assertStatus(422);
});
