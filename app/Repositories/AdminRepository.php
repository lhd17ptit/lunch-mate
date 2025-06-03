<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository extends BaseRepository
{
    public function getModel()
    {
        return Admin::class;
    }
}