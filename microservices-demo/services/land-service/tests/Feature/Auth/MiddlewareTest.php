<?php

test('rejects requests without service token', function () {
    $response = $this->postJson('/api/lands', []);

    $response->assertStatus(401);
});

test('rejects requests with wrong service token', function () {
    $response = $this->postJson('/api/lands', [], ['X-Service-Token' => 'wrong-token']);

    $response->assertStatus(401);
});

test('accepts requests with valid service token', function () {
    $response = $this->getJson('/api/lands', $this->serviceHeaders());

    $response->assertStatus(200);
});
