<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServingFoodItem extends Model
{
    use HasFactory;

    protected $table = 'order_serving_food_items';
    protected $guarded = [];

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}
