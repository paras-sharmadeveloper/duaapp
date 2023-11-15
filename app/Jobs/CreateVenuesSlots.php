<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{VenueSloting, VenueAddress};
use Carbon\Carbon;
class CreateVenuesSlots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $venueId;
    public $slotDuration;
    public function __construct($venueId, $slotDuration)
    {
        $this->venueId = $venueId;
        $this->slotDuration = $slotDuration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $venueAddress = VenueAddress::find($this->venueId); 

        if (!empty($venueAddress)) {

            $startTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_morning);
            $endTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_morning);

            // if evening has set then 
    
            if(!empty($venueAddress->slot_starts_at_evening) && !empty($venueAddress->slot_ends_at_evening)){
    
                $startTimeevng = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_evening);
                $endTimeEvn = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_evening);
    
                $currentTimeT = $startTimeevng;
                while ($currentTimeT < $endTimeEvn) {
                    $slotTime = $currentTimeT->format('H:i');
                    VenueSloting::create([
                        'venue_address_id' => $this->venueId,
                        'slot_time' => $slotTime,
                    ]);
                    $currentTimeT->addMinute($this->slotDuration); // Move to the next minute
                }
    
            } 
            // Create time slots
            // if morning has set then 
            $currentTime = $startTime;
            while ($currentTime < $endTime) {
                $slotTime = $currentTime->format('H:i');
                VenueSloting::create([
                    'venue_address_id' => $this->venueId,
                    'slot_time' => $slotTime,
                ]);
                $currentTime->addMinute($this->slotDuration); // Move to the next minute
            }
             
        }
 
       
        
    }
}
