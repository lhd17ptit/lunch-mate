<?php

namespace App\Repositories;

use App\Models\AdminFloor;

class AdminFloorRepository extends BaseRepository
{
    public function getModel()
    {
        return AdminFloor::class;
    }
}