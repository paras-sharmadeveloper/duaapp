<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Jobs\{CreateVenuesSlots}; 
use App\Models\{VenueSloting, VenueAddress};
class CreateFutureDateVenues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $dayToSet; 
    public $recuureingTill; 
    public $dataArr; 
    public $slotDuration; 
    public function __construct($dataArr,$dayToSet , $recuureingTill,$slotDuration)
    {
        $this->dataArr = $dataArr;
        $this->dayToSet = $dayToSet;
        $this->recuureingTill = $recuureingTill;
        $this->slotDuration = $slotDuration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $futureDates = [];
        foreach($this->dayToSet as $day){
            $futureDates = $this->RecurringDays($this->recuureingTill,$day);
        }
        $venueId = $this->dataArr['venue_id'];
        foreach($futureDates as $date ){
            $this->dataArr['venue_date'] = $date; 
            if (VenueAddress::whereDate('venue_date', $date)->where('venue_id', $venueId)->count() == 0) {
                // VenueAddress for the date does not exist, so create a new record
                $venueAddress = VenueAddress::create($this->dataArr);
                CreateVenuesSlots::dispatch($venueAddress->id, $this->slotDuration)
                    ->onQueue('create-slots')
                    ->onConnection('database');
            }
            
             
        } 
    }
    public function RecurringDays($tillMonths,$day){
        $currentDate = Carbon::now();
        $nextTwoMonths = $currentDate->copy()->addMonths($tillMonths);
         
        $mondaysInNextTwoMonths = [];

        while ($currentDate->lte($nextTwoMonths)) {
            if ($day=='monday' && $currentDate->dayOfWeek === Carbon::MONDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='tuesday' && $currentDate->dayOfWeek === Carbon::TUESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='wednesday' && $currentDate->dayOfWeek === Carbon::WEDNESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='thursday' && $currentDate->dayOfWeek === Carbon::THURSDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='friday' && $currentDate->dayOfWeek === Carbon::FRIDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='saturday' && $currentDate->dayOfWeek === Carbon::SATURDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='sunday' && $currentDate->dayOfWeek === Carbon::SUNDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            $currentDate->addDay();
        } 
        $allDates =[];

        foreach ($mondaysInNextTwoMonths as $monday) {
            $allDates[] = $monday->format('Y-m-d');  
        }
        return  $allDates; 
    }
}
