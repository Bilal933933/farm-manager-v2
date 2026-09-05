<?php

namespace Database\Factories;

use App\Enums\PartyRoleType;
use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartyRole>
 */
class PartyRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availableRoles = PartyRoleType::cases();

        return [
            'party_id' => Party::factory(),
            'role' => $availableRoles[array_rand($availableRoles)],
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the party role is for a specific party.
     */
    public function forParty(Party|string $party): static
    {
        return $this->state(fn (array $attributes) => [
            'party_id' => $party instanceof Party ? $party->id : $party,
        ]);
    }

    /**
     * Indicate that the party role is a supplier.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Supplier,
        ]);
    }

    /**
     * Indicate that the party role is a farmer.
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Farmer,
        ]);
    }

    /**
     * Indicate that the party role is an owner.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Owner,
        ]);
    }

    /**
     * Indicate that the party role is a tenant.
     */
    public function tenant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Tenant,
        ]);
    }

    /**
     * Indicate that the party role is a buyer.
     */
    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Buyer,
        ]);
    }

    /**
     * Indicate that the party role is a lessor.
     */
    public function lessor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Lessor,
        ]);
    }

    /**
     * Indicate that the party role is a contractor.
     */
    public function contractor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => PartyRoleType::Contractor,
        ]);
    }

    /**
     * Indicate that the party role has notes.
     */
    public function withNotes(?string $notes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $notes ?? $this->faker->paragraph(),
        ]);
    }

    /**
     * Indicate that the party role has no notes.
     */
    public function withoutNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => null,
        ]);
    }
}
