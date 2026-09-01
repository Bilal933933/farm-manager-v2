<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => str($name)->slug()->toString() . '-' . fake()->unique()->randomNumber(4),
            'license_number' => 'LIC-' . fake()->unique()->numerify('#####'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'address' => fake()->address(),
            'plan' => fake()->randomElement(['trial', 'basic', 'pro']),
            'trial_ends_at' => fake()->optional()->dateTimeBetween('now', '+14 days'),
            'settings' => ['locale' => 'ar'],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
