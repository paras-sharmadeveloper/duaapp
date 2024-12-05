<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppNotificationNumbers extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_notifications_numbers';
    protected $guarded = [];
}
