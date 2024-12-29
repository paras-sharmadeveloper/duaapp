<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorTempEntry extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function venueAddress()
    {
        return $this->belongsTo(VenueAddress::class, 'venueId');
    }
}
