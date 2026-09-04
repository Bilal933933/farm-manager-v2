<?php

namespace App\Actions\Land;

use App\Models\Land;
use Illuminate\Support\Facades\DB;

class UpdateLandAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Land $land, array $data): Land
    {
        return DB::transaction(function () use ($land, $data) {
            $land->update($data);

            return $land->refresh();
        });
    }
}
