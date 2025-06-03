<?php

namespace App\Repositories;

use App\Models\FoodItem;

class FoodItemRepository extends BaseRepository
{
    public function getModel()
    {
        return FoodItem::class;
    }
}