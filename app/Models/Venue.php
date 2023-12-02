<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;
    protected $fillable = ['country_name', 'address', 'flag_path', 'iso'];

    static function getVenue($id)
    {
        return self::where(['id' => $id])->get()->first();
    }

    public function getAddress()
    {
        return $this->hasMany(VenueAddress::class, 'venue_id');
    }

    public function getTimezone()
    {
        return $this->belongsTo(Venue::class, 'country_name', 'country_code');
    }

    public function venueCity()
    {
        return $this->belongsTo(VenueStateCity::class,'venue_id','id');
    } 
}
