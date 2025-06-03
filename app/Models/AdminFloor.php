<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFloor extends Model
{
    use HasFactory;

    protected $table = 'admin_floor';
    protected $guarded = [];
}
