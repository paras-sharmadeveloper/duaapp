<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'states'; 
    protected $guarded=[];

    public function cities()
    {
        return $this->hasMany(City::class, 'id', 'state_id');
    }
}
