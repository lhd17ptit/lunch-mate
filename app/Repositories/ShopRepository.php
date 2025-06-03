<?php

namespace App\Repositories;

use App\Models\Shop;

class ShopRepository extends BaseRepository
{
    public function getModel()
    {
        return Shop::class;
    }
}