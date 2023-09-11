<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueAddress extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function venueSloting()
    {
        return $this->hasMany(VenueSloting::class);
    }
}
