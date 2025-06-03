<?php

namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository extends BaseRepository
{
    public function getModel()
    {
        return MenuItem::class;
    }
}