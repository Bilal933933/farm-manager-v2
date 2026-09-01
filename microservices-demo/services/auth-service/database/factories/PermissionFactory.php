<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $group = fake()->randomElement(['lands', 'inventory', 'procurement', 'finance', 'users', 'companies']);
        return [
            'name' => $group . '.' . fake()->unique()->word(),
            'group_name' => $group,
            'guard_name' => 'web',
            'description' => fake()->sentence(),
        ];
    }
}
