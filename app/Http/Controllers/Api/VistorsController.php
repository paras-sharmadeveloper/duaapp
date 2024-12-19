<?php

namespace App\Http\Controllers;

use App\Models\{Vistors, VisitorBooking};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorBookingMail;

class VistorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $vistors = Vistors::with('slot')->get();
        // echo "<pre>"; print_r( $vistors); die; 
        return view('patient.index', compact('vistors'));
    }

    public function list()
    {
        $bookings = VisitorBooking::all(); // You can customize this query as needed
        return view('visitors.list', compact('bookings'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('visitors.create');
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'purpose' => 'required|string',
            'visit_datetime' => 'required',
        ]);

        if ($id) {
            // Editing an existing booking
            $booking = VisitorBooking::findOrFail($id);
            $booking->update($validatedData);
        } else {
            // Creating a new booking
            $booking = VisitorBooking::create($validatedData);
        }
        $validatedData['subject'] = 'Booking With KahayFaqeer';

        // Send email
        Mail::to($validatedData['email'])->send(new VisitorBookingMail($validatedData));

        // Redirect back with a success message
        return redirect()->route('booking.create')->with('success', 'Booking ' . ($id ? 'updated' : 'created') . ' successfully!');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Vistors $vistors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $booking = VisitorBooking::findOrFail($id);
        return view('visitors.create', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vistors $vistors)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function DeleteNow($id)
    {
        $booking = VisitorBooking::find($id);
        $booking->delete();
        return redirect()->back()->with('success', 'Booking deleted successfully!');
    }
}
