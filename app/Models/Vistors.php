<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vistors extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function slot()
    {
        return $this->belongsTo(VenueSloting::class, 'slot_id');
    }
    public function venueSloting()
    {
        return $this->belongsTo(VenueSloting::class, 'slot_id');  
    }
     
}
