<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::factory()->create([
            'name' => 'الريان الزراعية',
            'slug' => 'alrayan',
            'license_number' => 'LIC-12345',
            'phone' => '0500000001',
            'email' => 'info@alrayan.test',
            'address' => 'الرياض - الجنوب',
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'settings' => ['locale' => 'ar'],
            'is_active' => true,
        ]);

        // شركات إضافية للاختبار (اختياري)
        // Company::factory()->count(3)->create();
    }
}
