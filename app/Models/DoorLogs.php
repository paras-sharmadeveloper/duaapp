<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorLogs extends Model
{
    use HasFactory;

    public function visitor()
    {
        return $this->belongsTo(Vistors::class, 'SCode', 'booking_uniqueid');
    }
}
