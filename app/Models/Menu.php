<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $guarded = [];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function foodCategories()
    {
        return $this->hasMany(FoodCategory::class, 'shop_id', 'shop_id');
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id');
    }
}
