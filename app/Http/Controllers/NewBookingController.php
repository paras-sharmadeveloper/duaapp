<?php

namespace App\Http\Controllers;
use App\Models\{Venue, Reason , VenueSloting, VenueAddress, Vistors, Country, User, Notification, Timezone, Ipinformation, VenueStateCity};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;

class NewBookingController extends Controller
{
    //

    public function index( Request $request, $locale = ''){

        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }

        $therapistRole = Role::where('name', 'therapist')->first();
        $VenueList = Venue::all();
        $countryList = Country::all();
        $therapists = $therapistRole->users;
        $timezones = Country::with('timezones')->get();
        $reasons = Reason::where(['type' => 'announcement'])->first();
        return view('frontend.multistep.index', compact('VenueList', 'countryList', 'therapists', 'timezones', 'locale', 'reasons'));
    }
}
