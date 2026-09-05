<?php

describe('Party API V1', function () {
    beforeEach(function () {
        $this->party = $this->createParty('comp-1', ['name' => 'Test Party']);
    });

    describe('GET /api/v1/parties', function () {
        test('list parties with pagination', function () {
            $this->createParty('comp-1', ['name' => 'Party 1']);
            $this->createParty('comp-1', ['name' => 'Party 2']);
            $this->createParty('comp-1', ['name' => 'Party 3']);

            $response = $this->getJson('/api/v1/parties');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'email', 'phone', 'status'],
                    ],
                    'links',
                    'meta' => ['total', 'per_page', 'current_page'],
                ]);
        });

        test('filter parties by status', function () {
            $this->createParty('comp-1', ['name' => 'Active', 'status' => 'active']);
            $this->createParty('comp-1', ['name' => 'Inactive', 'status' => 'inactive']);

            $response = $this->getJson('/api/v1/parties?status=active');

            $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
        });

        test('search parties by name', function () {
            $this->createParty('comp-1', ['name' => 'John Doe']);
            $this->createParty('comp-1', ['name' => 'Jane Smith']);

            $response = $this->getJson('/api/v1/parties?search=John');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });

        test('sort parties by name', function () {
            $this->createParty('comp-1', ['name' => 'Zebra']);
            $this->createParty('comp-1', ['name' => 'Apple']);

            $response = $this->getJson('/api/v1/parties?sort_by=name&sort_order=asc');

            $response->assertStatus(200);
            $data = $response->json('data');
            expect($data[0]['name'])->toBe('Apple');
        });
    });

    describe('POST /api/v1/parties', function () {
        test('create party', function () {
            $payload = [
                'name' => 'New Party',
                'email' => 'party@example.com',
                'phone' => '123456789',
                'status' => 'active',
            ];

            $response = $this->postJson('/api/v1/parties', $payload);

            $response->assertStatus(201)
                ->assertJsonFragment(['name' => 'New Party']);

            $this->assertDatabaseHas('parties', ['email' => 'party@example.com']);
        });

        test('validate required fields', function () {
            $response = $this->postJson('/api/v1/parties', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });

        test('prevent duplicate email', function () {
            $this->createParty('comp-1', ['email' => 'duplicate@example.com']);

            $response = $this->postJson('/api/v1/parties', [
                'name' => 'Another',
                'email' => 'duplicate@example.com',
            ]);

            $response->assertStatus(422);
        });
    });

    describe('GET /api/v1/parties/{id}', function () {
        test('show party', function () {
            $response = $this->getJson("/api/v1/parties/{$this->party->id}");

            $response->assertStatus(200)
                ->assertJsonFragment(['id' => $this->party->id]);
        });

        test('return 404 for non-existent party', function () {
            $response = $this->getJson('/api/v1/parties/invalid-id');

            $response->assertStatus(404);
        });
    });

    describe('PUT /api/v1/parties/{id}', function () {
        test('update party', function () {
            $payload = ['name' => 'Updated Name'];

            $response = $this->putJson("/api/v1/parties/{$this->party->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Updated Name']);

            $this->assertDatabaseHas('parties', ['id' => $this->party->id, 'name' => 'Updated Name']);
        });

        test('validate on update', function () {
            $response = $this->putJson("/api/v1/parties/{$this->party->id}", ['name' => '']);

            $response->assertStatus(422);
        });
    });

    describe('DELETE /api/v1/parties/{id}', function () {
        test('soft delete party', function () {
            $response = $this->deleteJson("/api/v1/parties/{$this->party->id}");

            $response->assertStatus(200);

            $this->assertSoftDeleted('parties', ['id' => $this->party->id]);
        });
    });

    describe('DELETE /api/v1/parties/bulk/delete', function () {
        test('bulk delete parties', function () {
            $party1 = $this->createParty('comp-1', ['name' => 'P1']);
            $party2 = $this->createParty('comp-1', ['name' => 'P2']);

            $response = $this->deleteJson('/api/v1/parties/bulk/delete', [
                'ids' => [$party1->id, $party2->id],
            ]);

            $response->assertStatus(200)
                ->assertJsonFragment(['deleted' => 2]);

            $this->assertSoftDeleted('parties', ['id' => $party1->id]);
            $this->assertSoftDeleted('parties', ['id' => $party2->id]);
        });
    });
});
