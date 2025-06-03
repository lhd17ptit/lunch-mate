<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'food_items';
    protected $guarded = [];

    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id');
    }
}
