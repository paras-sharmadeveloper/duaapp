<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    use HasFactory;
    protected $fillable = ['job_id', 'status', 'result'];

    protected $casts = [
        'result' => 'array',
    ];
}
