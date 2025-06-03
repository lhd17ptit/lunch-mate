<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'floors';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'floor_id');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_floor', 'floor_id', 'admin_id');
    }
}
