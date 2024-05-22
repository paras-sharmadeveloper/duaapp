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
    public function __construct($venueId)
    {
        $this->venueId = $venueId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $venueAddress = VenueAddress::find($this->venueId);

        if (!empty($venueAddress)) {

          //  $startTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_morning);
          //  $endTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_morning);

            $duaSlots = $venueAddress->dua_slots;
            $dumSlots = $venueAddress->dum_slots;


            $working_lady_dum = $venueAddress->working_lady_dum;
            $working_lady_dua = $venueAddress->working_lady_dua;

            for($token=1; $token<=$duaSlots; $token++){

                VenueSloting::create([
                    'venue_address_id' => $this->venueId,
                    'slot_time' =>  date("Y-m-d H:i:s"),
                    'token_id' => $token,
                    'type' => 'dua'
                ]);

            }

            for($token=1001; $token<=$dumSlots; $token++){

                VenueSloting::create([
                    'venue_address_id' => $this->venueId,
                    'slot_time' => date("Y-m-d H:i:s"),
                    'token_id' => $token,
                    'type' => 'dum'
                ]);
            }

            for($token=8001; $token<=$working_lady_dua; $token++){

                VenueSloting::create([
                    'venue_address_id' => $this->venueId,
                    'slot_time' => date("Y-m-d H:i:s"),
                    'token_id' => $token,
                    'type' => 'working_lady_dua'
                ]);
            }

            for($token=1801; $token<=$working_lady_dum; $token++){

                VenueSloting::create([
                    'venue_address_id' => $this->venueId,
                    'slot_time' => date("Y-m-d H:i:s"),
                    'token_id' => $token,
                    'type' => 'working_lady_dum'
                ]);
            }



            // while ($currentTime < $endTime) {
            //     $slotTime = $currentTime->format('H:i');
            //     VenueSloting::create([
            //         'venue_address_id' => $this->venueId,
            //         'slot_time' => $slotTime,
            //         'token_id' => $tokenId,
            //     ]);
            //     $currentTime->addMinute($this->slotDuration); // Move to the next minute
            //     $tokenId++;
            // }

        }



    }
}
