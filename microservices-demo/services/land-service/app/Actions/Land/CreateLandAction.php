<?php

namespace App\Actions\Land;

use App\Models\Land;
use Illuminate\Support\Facades\DB;

class CreateLandAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, string $companyId): Land
    {
        return DB::transaction(function () use ($data, $companyId) {
            $land = Land::create([...$data, 'company_id' => $companyId]);

            return $land->refresh();
        });
    }
}
