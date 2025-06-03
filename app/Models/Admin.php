<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes;

    const ADMIN = 1;
    const PARTNER = 2;
    
    protected $table = 'admins';
    protected $guarded = [];

    public function floor()
    {
        return $this->belongsToMany(Floor::class, 'admin_floor')->limit(1);
    }

    public function getSingleFloorAttribute()
    {
        return $this->floor()->first();
    }
}
