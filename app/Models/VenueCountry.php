<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueCountry extends Model
{
    use HasFactory;
    protected $table='venues'; 
    protected $guarded=[];
}
