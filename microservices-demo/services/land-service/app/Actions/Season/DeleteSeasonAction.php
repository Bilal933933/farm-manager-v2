<?php

namespace App\Actions\Season;

use App\Models\Season;

class DeleteSeasonAction
{
    public function execute(Season $season): void
    {
        $season->delete();
    }
}
