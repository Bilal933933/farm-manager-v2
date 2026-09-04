<?php

namespace App\Actions\Crop;

use App\Models\Crop;

class DeleteCropAction
{
    public function execute(Crop $crop): void
    {
        $crop->delete();
    }
}
