<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueSloting extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table = 'venues_sloting';
    public function venueAddress()
    {
        return $this->belongsTo(VenueAddress::class, 'venue_address_id');
    }
    public function visitors()
    {
        return $this->hasMany(Vistors::class, 'slot_id');
    }
    public function visitor()
    {
        return $this->belongsTo(Vistors::class, 'slot_id' , 'id');
    }

}
