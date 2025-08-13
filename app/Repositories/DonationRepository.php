<?php

namespace App\Repositories;

use App\Models\Donation;

class DonationRepository extends BaseRepository
{
    public function getModel()
    {
        return Donation::class;
    }
}