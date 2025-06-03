<?php

namespace App\Repositories;

use App\Models\FoodCategory;

class FoodCategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return FoodCategory::class;
    }
}