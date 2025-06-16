<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';
    protected $guarded = [];

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id', 'id');
    }
}
