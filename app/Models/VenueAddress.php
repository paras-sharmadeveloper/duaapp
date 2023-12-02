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
   
    // get address by venue address id
    static function getAddress($id){
        return self::where(['id' => $id])->get()->first();
    }
    public function user()
    {
        return $this->belongsTo(User::class,'therapist_id');
    } 

    public function thripist()
    {
        return $this->belongsTo(User::class,'therapist_id');
    } 
    public function siteadmin()
    {
        return $this->belongsTo(User::class,'siteadmin_id');
    } 

    public function combinationData()
    {
        return $this->belongsTo(VenueStateCity::class,'combination_id','id');
       
    }
    
}
