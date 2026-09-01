<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'alrayan')->firstOrFail();
        $perms = Permission::pluck('id', 'name');

        $manager = Role::firstOrCreate(
            ['company_id' => $company->id, 'slug' => 'manager'],
            ['name' => 'مدير', 'description' => 'مدير الشركة - كل الصلاحيات', 'is_system' => true]
        );
        $accountant = Role::firstOrCreate(
            ['company_id' => $company->id, 'slug' => 'accountant'],
            ['name' => 'محاسب', 'description' => 'محاسب', 'is_system' => true]
        );
        $worker = Role::firstOrCreate(
            ['company_id' => $company->id, 'slug' => 'worker'],
            ['name' => 'عامل', 'description' => 'عامل ميداني', 'is_system' => true]
        );

        // مدير: كل الصلاحيات
        $manager->permissions()->sync($perms->values()->all());

        // محاسب: المالية والمخزون
        $accountant->permissions()->sync(
            collect(['finance.view','finance.create','finance.approve','reports.view','inventory.view'])
                ->map(fn ($n) => $perms[$n] ?? null)->filter()->values()->all()
        );

        // عامل: عرض فقط
        $worker->permissions()->sync(
            collect(['lands.view','seasons.view','inventory.view'])
                ->map(fn ($n) => $perms[$n] ?? null)->filter()->values()->all()
        );
    }
}
