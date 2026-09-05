<?php

namespace Database\Seeders;

use App\Enums\PartyRoleType;
use App\Models\Party;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parties for company 1
        $company1 = 'company-1';

        // Create 5 active parties, each with 1 role to avoid unique constraint violations
        foreach (range(1, 5) as $i) {
            $party = Party::factory()
                ->forCompany($company1)
                ->active()
                ->create();

            // Add 1-2 random unique roles
            $availableRoles = PartyRoleType::cases();
            $numberOfRoles = rand(1, 2);
            $selectedRoles = collect($availableRoles)->random($numberOfRoles);

            foreach ($selectedRoles as $role) {
                $party->roles()->create([
                    'role' => $role,
                    'notes' => fake()->optional(0.5)->sentence(),
                ]);
            }
        }

        // Create 2 inactive parties
        Party::factory()
            ->forCompany($company1)
            ->inactive()
            ->count(2)
            ->create();

        // Create a supplier with full details
        $supplier = Party::factory()
            ->forCompany($company1)
            ->withNotes('Trusted supplier for agricultural equipment')
            ->create([
                'name' => 'ABC Agricultural Supplies',
            ]);

        $supplier->roles()->create([
            'role' => PartyRoleType::Supplier,
            'notes' => 'Primary equipment supplier',
        ]);

        // Create a farmer with multiple roles
        $farmer = Party::factory()
            ->forCompany($company1)
            ->create([
                'name' => 'Ahmed Farm',
            ]);

        $farmer->roles()->createMany([
            ['role' => PartyRoleType::Farmer, 'notes' => 'Wheat and corn specialist'],
            ['role' => PartyRoleType::Owner, 'notes' => 'Owns 50 hectares'],
        ]);

        // Create parties for company 2
        $company2 = 'company-2';

        Party::factory()
            ->forCompany($company2)
            ->active()
            ->count(3)
            ->create();

        // Create minimal parties (only required fields)
        Party::factory()
            ->forCompany($company1)
            ->minimal()
            ->count(2)
            ->create();
    }
}
