<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OAuthToken extends Model
{
    protected $guarded = [];

    public function isExpired(): bool
    {
        return empty($this->expires_at) || $this->expires_at && $this->expires_at->isPast();
    }

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
