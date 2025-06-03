<?php

namespace App\Repositories;

use App\Models\OrderServingFoodItem;

class OrderServingFoodItemRepository extends BaseRepository
{
    public function getModel()
    {
        return OrderServingFoodItem::class;
    }
}