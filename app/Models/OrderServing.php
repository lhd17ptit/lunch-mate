<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServing extends Model
{
    use HasFactory;

    protected $table = 'order_servings';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderServingFoodItems()
    {
        return $this->hasMany(OrderServingFoodItem::class, 'order_serving_id');
    }

    public function getTip(){

        $isFirst = ! static::where('order_id', $this->order_id)
                    ->where('id', '<', $this->id)
                    ->exists();
        if($isFirst){
            return $this->order->tip ?? 0;
        }
        return 0; // not first serving of order - tip = 0
    }
}
