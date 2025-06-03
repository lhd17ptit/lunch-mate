<?php

namespace App\Repositories;

use App\Models\Floor;

class FloorRepository extends BaseRepository
{
    public function getModel()
    {
        return Floor::class;
    }
}