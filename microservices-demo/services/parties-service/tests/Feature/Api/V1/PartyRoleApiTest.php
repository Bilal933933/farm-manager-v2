<?php

describe('Party Role API V1', function () {
    beforeEach(function () {
        $this->party = $this->createParty('comp-1', ['name' => 'Test Party']);
    });

    describe('GET /api/v1/parties/{party}/roles', function () {
        test('list party roles', function () {
            $this->createPartyRole($this->party, ['type' => 'supplier']);
            $this->createPartyRole($this->party, ['type' => 'farmer']);

            $response = $this->getJson("/api/v1/parties/{$this->party->id}/roles");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'party_id', 'type'],
                    ],
                ]);
        });

        test('return empty array when no roles', function () {
            $response = $this->getJson("/api/v1/parties/{$this->party->id}/roles");

            $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
        });
    });

    describe('POST /api/v1/parties/{party}/roles', function () {
        test('add role to party', function () {
            $payload = ['type' => 'supplier'];

            $response = $this->postJson("/api/v1/parties/{$this->party->id}/roles", $payload);

            $response->assertStatus(201)
                ->assertJsonFragment(['type' => 'supplier']);

            $this->assertDatabaseHas('party_roles', [
                'party_id' => $this->party->id,
                'type' => 'supplier',
            ]);
        });

        test('validate role type', function () {
            $response = $this->postJson("/api/v1/parties/{$this->party->id}/roles", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });

        test('prevent duplicate role', function () {
            $this->createPartyRole($this->party, ['type' => 'supplier']);

            $response = $this->postJson("/api/v1/parties/{$this->party->id}/roles", [
                'type' => 'supplier',
            ]);

            $response->assertStatus(422);
        });
    });

    describe('DELETE /api/v1/parties/{party}/roles/{role}', function () {
        test('remove role from party', function () {
            $role = $this->createPartyRole($this->party, ['type' => 'supplier']);

            $response = $this->deleteJson("/api/v1/parties/{$this->party->id}/roles/{$role->id}");

            $response->assertStatus(200);

            $this->assertSoftDeleted('party_roles', ['id' => $role->id]);
        });

        test('return 404 for non-existent role', function () {
            $response = $this->deleteJson("/api/v1/parties/{$this->party->id}/roles/invalid-id");

            $response->assertStatus(404);
        });
    });
});
