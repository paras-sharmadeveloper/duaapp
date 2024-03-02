<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
class Timezone extends Model
{
    use HasFactory;
    protected $table = 'timezone';



    public function countryName(){
       return $this->belongsTo(Country::class,'country_code','iso');
    }

    public function getTimezone()
    {
        return $this->belongsTo(Venue::class,'country_code' , 'iso');
    }

}
