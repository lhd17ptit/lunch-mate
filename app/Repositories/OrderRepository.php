<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function getModel()
    {
        return Order::class;
    }
}