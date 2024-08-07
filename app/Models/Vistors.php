<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vistors extends Model
{
    use HasFactory;
    protected $table = 'visitors';
    protected $guarded = [];
    protected $dates = ['meeting_start_at', 'meeting_ends_at'];

    public function slot()
    {
        return $this->belongsTo(VenueSloting::class, 'slot_id');
    }
    public function venueSloting()
    {
        return $this->belongsTo(VenueSloting::class, 'slot_id');
    }

    public function scopeAheadOfVisitor($query)
    {
        // Count visitors where meeting_start_at is null (ahead)
        return $query->whereNull('meeting_start_at')->count();
    }

    public function scopeAlreadyServed($query)
    {
        // Count visitors where meeting_ends_at is not null (already served)
        return $query->whereNotNull('meeting_ends_at')->count();
    }

    public function doorLogs()
    {
        return $this->hasMany(DoorLogs::class, 'SCode', 'booking_uniqueid');
    }

}
