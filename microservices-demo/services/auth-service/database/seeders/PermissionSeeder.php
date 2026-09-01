<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'lands.view', 'group_name' => 'lands', 'description' => 'عرض الأراضي'],
            ['name' => 'lands.create', 'group_name' => 'lands', 'description' => 'إنشاء أرض'],
            ['name' => 'lands.update', 'group_name' => 'lands', 'description' => 'تعديل أرض'],
            ['name' => 'lands.delete', 'group_name' => 'lands', 'description' => 'حذف أرض'],
            ['name' => 'seasons.view', 'group_name' => 'lands', 'description' => 'عرض المواسم'],
            ['name' => 'seasons.create', 'group_name' => 'lands', 'description' => 'إنشاء موسم'],
            ['name' => 'seasons.update', 'group_name' => 'lands', 'description' => 'تعديل موسم'],
            ['name' => 'seasons.delete', 'group_name' => 'lands', 'description' => 'حذف موسم'],
            ['name' => 'inventory.view', 'group_name' => 'inventory', 'description' => 'عرض المخزون'],
            ['name' => 'inventory.create', 'group_name' => 'inventory', 'description' => 'إنشاء منتج'],
            ['name' => 'inventory.update', 'group_name' => 'inventory', 'description' => 'تعديل مخزون'],
            ['name' => 'inventory.delete', 'group_name' => 'inventory', 'description' => 'حذف مخزون'],
            ['name' => 'inventory.movements', 'group_name' => 'inventory', 'description' => 'حركات المخزون'],
            ['name' => 'procurement.view', 'group_name' => 'procurement', 'description' => 'عرض المشتريات'],
            ['name' => 'procurement.create', 'group_name' => 'procurement', 'description' => 'إنشاء أمر شراء'],
            ['name' => 'procurement.approve', 'group_name' => 'procurement', 'description' => 'اعتماد مشتريات'],
            ['name' => 'finance.view', 'group_name' => 'finance', 'description' => 'عرض المحاسبة'],
            ['name' => 'finance.create', 'group_name' => 'finance', 'description' => 'إنشاء قيد'],
            ['name' => 'finance.approve', 'group_name' => 'finance', 'description' => 'اعتماد قيود'],
            ['name' => 'reports.view', 'group_name' => 'finance', 'description' => 'عرض التقارير'],
            ['name' => 'users.view', 'group_name' => 'users', 'description' => 'عرض المستخدمين'],
            ['name' => 'users.create', 'group_name' => 'users', 'description' => 'إضافة مستخدم'],
            ['name' => 'users.update', 'group_name' => 'users', 'description' => 'تعديل مستخدم'],
            ['name' => 'users.delete', 'group_name' => 'users', 'description' => 'حذف مستخدم'],
            ['name' => 'roles.view', 'group_name' => 'users', 'description' => 'عرض الأدوار'],
            ['name' => 'roles.manage', 'group_name' => 'users', 'description' => 'إدارة الأدوار والصلاحيات'],
            ['name' => 'companies.view', 'group_name' => 'companies', 'description' => 'عرض الشركات'],
            ['name' => 'companies.update', 'group_name' => 'companies', 'description' => 'تعديل شركتي'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                array_merge($perm, ['guard_name' => 'web'])
            );
        }
    }
}
