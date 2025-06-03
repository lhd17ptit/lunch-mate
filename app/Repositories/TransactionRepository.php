<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{
    public function getModel()
    {
        return Transaction::class;
    }
}