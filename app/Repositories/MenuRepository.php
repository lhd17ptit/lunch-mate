<?php

namespace App\Repositories;

use App\Models\Menu;

class MenuRepository extends BaseRepository
{
    public function getModel()
    {
        return Menu::class;
    }
}