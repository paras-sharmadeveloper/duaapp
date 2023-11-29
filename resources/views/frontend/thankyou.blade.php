@extends('layouts.guest')
@section('content') 
<style>
    body {
    font-family: "Open Sans", sans-serif;
    background: rgb(29, 29, 29) !important;
    color: #444444;
}
</style>
<div class="vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-12">
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
            <div class="text-center">
                <h1>Your meeting is confirmed !</h1>
                <p>Thank  you<b> {{ $userBooking->fname }}</b>,</p>
                <p>You will get confirmation email shortly</p>
                <p>Your <b> token number is</b>{{ $userBooking->booking_number }} </p><br>
                <p>God bless you!</p> <br>
                <p> Team <a href="https://kahayfaqeer.org/" target="_blank" > KahayFaqeer.org </a></p>
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
