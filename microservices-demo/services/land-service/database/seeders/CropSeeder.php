<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * @var array<int, array<string, string>>
     */
    private array $crops = [
        ['name' => 'أرز', 'unit' => 'kg'],
        ['name' => 'قمح', 'unit' => 'kg'],
        ['name' => 'ذرة', 'unit' => 'kg'],
        ['name' => 'شعير', 'unit' => 'kg'],
        ['name' => 'قطن', 'unit' => 'kg'],
        ['name' => 'طماطم', 'unit' => 'kg'],
        ['name' => 'بطاطس', 'unit' => 'kg'],
        ['name' => 'خيار', 'unit' => 'kg'],
        ['name' => 'فلفل', 'unit' => 'kg'],
        ['name' => 'برتقال', 'unit' => 'kg'],
        ['name' => 'عنب', 'unit' => 'kg'],
        ['name' => 'موز', 'unit' => 'kg'],
    ];

    public function run(): void
    {
        foreach ($this->crops as $crop) {
            Crop::firstOrCreate(['name' => $crop['name']], $crop);
        }
    }
}
