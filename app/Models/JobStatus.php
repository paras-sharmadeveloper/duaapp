<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    use HasFactory;
    protected $fillable = ['job_id', 'status', 'result','user_inputs','entry_created'];

    protected $casts = [
        'result' => 'array',
        'user_inputs' => 'array'
    ];
}
