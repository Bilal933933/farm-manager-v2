<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin بدون شركة
        User::firstOrCreate(
            ['email' => 'super@basira.test'],
            [
                'company_id' => null,
                'role_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $company = Company::where('slug', 'alrayan')->firstOrFail();
        $managerRole = Role::where('company_id', $company->id)->where('slug', 'manager')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'k@alrayan.test'],
            [
                'company_id' => $company->id,
                'role_id' => $managerRole->id,
                'name' => 'خالد المدير',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
