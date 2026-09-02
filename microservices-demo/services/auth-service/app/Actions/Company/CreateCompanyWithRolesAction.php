<?php

namespace App\Actions\Company;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class CreateCompanyWithRolesAction
{
    public function execute(string $name, ?string $slug = null): Company
    {
        $company = Company::create([
            'name' => $name,
            'slug' => $slug ?: (Str::slug($name) ?: 'company').'-'.Str::random(4),
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'is_active' => true,
        ]);

        // 3 أدوار ثابتة - نفس منطق RoleSeeder
        $perms = Permission::all()->keyBy('name');

        // مدير: كل الصلاحيات
        $manager = Role::create([
            'company_id' => $company->id,
            'name' => 'مدير',
            'slug' => 'manager',
            'is_system' => true,
        ]);
        $manager->permissions()->sync($perms->pluck('id'));

        // محاسب: المالية والمخزون والمستخدمين والشركات
        $accountant = Role::create([
            'company_id' => $company->id,
            'name' => 'محاسب',
            'slug' => 'accountant',
            'is_system' => true,
        ]);
        $accountant->permissions()->sync(
            $perms->whereIn('name', ['finance.view', 'finance.create', 'finance.approve', 'companies.view', 'users.view', 'reports.view', 'inventory.view'])->pluck('id')
        );

        // عامل: عرض فقط
        $worker = Role::create([
            'company_id' => $company->id,
            'name' => 'عامل',
            'slug' => 'worker',
            'is_system' => true,
        ]);
        $worker->permissions()->sync(
            $perms->whereIn('name', ['lands.view', 'inventory.view', 'procurement.view'])->pluck('id')
        );

        return $company->load('roles');
    }
}
