@extends('layouts.guest')
@section('content') 
<style>
    body {
    font-family: "Open Sans", sans-serif;
    background: rgb(29, 29, 29) !important;
    color: #444444;
}
@media only screen and (max-width: 600px) {
.logo {
    margin-top: 50% !important;
}
}
</style>
<div class="vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-12">
        <div class="d-flex justify-content-center py-2 logo">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                <img src="https://kahayfaqeer.org/assets/kahe-faqeer-white-1.png" alt="">

                <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
            </a>
        </div>
        <div class="border border-3 border-success"></div>
        
        <div class="card  bg-white shadow p-5">
           
            <div class="mb-4 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="75" height="75"
                    fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path
                        d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                </svg>
            </div>
            @php 
                $venueAddress = $userBooking->slot->venueAddress;
                $slottime =$userBooking->slot->slot_time;
                $userSelectedTimezone = \Carbon\Carbon::parse($venueAddress->venue_date.' '.$slottime,$venueAddress->timezone);
                 
                $userSelectedTimezone->timezone($userBooking->user_timezone); 
                $userTimezoneFormat = $userSelectedTimezone->format('l F j, Y ⋅ g:i a') . ' – ' . $userSelectedTimezone->addMinutes(30)->format('g:ia');
            @endphp
            <div class="text-center">
                <h1>Your meeting is confirmed !</h1>
                <p>Thank  you<b> {{ $userBooking->fname }}</b>,</p>
                <p>You will get confirmation email shortly</p>
                <p>Your token number is <b>{{ $userBooking->booking_number }}</b>  </p> 
                <p>{{ $userTimezoneFormat . '('.$venueAddress->timezone.')', }}</p>
                <p>God bless you!</p>  
                <p>Subscribe to Syed Sarfraz A. Shah Official YouTube Channel <a href="https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1" target="_blank" > here </a></p>
                <p>Subscribe to Syed Sarfraz A. Shah Official WhatsApp Channel <a href=" https://whatsapp.com/channel/0029Va9FvbdGE56jAmX0fo2w" target="_blank" > here </a></p>
                <a href="{{ route('book.show') }}" class="btn btn-outline-success mt-4">Back To Booking Form</a> 
            </div>
        </div>
    </div>
</div> 


@endsection
@section('page-script') 
<script>
    document.title = "KahayFaqeer.com | Booking Thankyou";
  </script>
@endsection
