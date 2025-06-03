<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServingFoodItem extends Model
{
    use HasFactory;

    protected $table = 'order_serving_food_items';
    protected $guarded = [];
}
