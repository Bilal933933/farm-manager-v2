<?php

namespace App\Actions\Crop;

use App\Models\Crop;
use Illuminate\Support\Facades\DB;

class CreateCropAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Crop
    {
        return DB::transaction(function () use ($data) {
            $crop = Crop::create($data);

            return $crop->refresh();
        });
    }
}
