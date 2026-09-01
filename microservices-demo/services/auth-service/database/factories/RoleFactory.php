<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->randomElement(['مدير', 'محاسب', 'عامل', 'مشرف']),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'is_system' => false,
        ];
    }

    public function system(): static
    {
        return $this->state(fn (array $attributes) => ['is_system' => true]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => ['company_id' => $company->id]);
    }
}
