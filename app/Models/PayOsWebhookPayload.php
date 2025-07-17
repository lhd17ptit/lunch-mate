<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayOsWebhookPayload extends Model
{
    use HasFactory;

    protected $table = 'payos_webhook_payloads';
    protected $guarded = [];
}
