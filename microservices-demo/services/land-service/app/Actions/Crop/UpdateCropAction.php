<?php

namespace App\Actions\Crop;

use App\Models\Crop;
use Illuminate\Support\Facades\DB;

class UpdateCropAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Crop $crop, array $data): Crop
    {
        return DB::transaction(function () use ($crop, $data) {
            $crop->update($data);

            return $crop->refresh();
        });
    }
}
