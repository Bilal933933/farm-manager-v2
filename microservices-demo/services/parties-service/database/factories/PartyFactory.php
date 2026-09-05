<?php

namespace Database\Factories;

use App\Enums\PartyStatus;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'phone' => $this->faker->unique()->numerify('01#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'status' => PartyStatus::Active,
        ];
    }

    /**
     * Indicate that the party is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PartyStatus::Inactive,
        ]);
    }

    /**
     * Indicate that the party is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PartyStatus::Active,
        ]);
    }

    /**
     * Indicate that the party belongs to a specific company.
     */
    public function forCompany(string $companyId): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $companyId,
        ]);
    }

    /**
     * Indicate that the party has no email.
     */
    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * Indicate that the party has no address.
     */
    public function withoutAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => null,
        ]);
    }

    /**
     * Indicate that the party has notes.
     */
    public function withNotes(?string $notes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $notes ?? $this->faker->paragraph(),
        ]);
    }

    /**
     * Indicate that the party has minimal required fields only.
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'address' => null,
            'notes' => null,
        ]);
    }
}
