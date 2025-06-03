<?php

namespace App\Repositories;

use App\Models\OrderServing;

class OrderServingRepository extends BaseRepository
{
    public function getModel()
    {
        return OrderServing::class;
    }
}