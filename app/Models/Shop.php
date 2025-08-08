<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';
    protected $guarded = [];

    const LUNCH_MATE = 'lunch-mate';
    const BREAKFAST_MATE = 'breakfast-mate';
    const AFTERNOON_MATE = 'afternoon-mate';
}
