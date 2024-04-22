@extends('layouts.guest')

@section('content')
<style>
  body{background-color:rgb(29, 29, 29);font-family:Karla,sans-serif}
</style>
<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-lg-9 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logoo  d-flex align-items-center wuto">
                  {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                  <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? ''}}</span> -->
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Thanks for Coming . Please confirm your spot below</h5>
                    <p class="text-center small">Enter Your Registered Mobile/Email and Booking ID To confirm Spot.</p>
                  </div>
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                     @endif
                     @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                     @endif
                      @if(session()->has('email_verified_error'))
                        <div class="alert alert-danger">
                            {{ session()->get('email_verified_error') }}
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#basicModal">Resend</a>

                        </div>
                     @endif


                  <form class="row g-3 needs-validation " action="{{ route('booking.confirm-spot.post') }}" method="post">

                     @csrf

                    <div class="col-12">
                         <label for="yourPassword" class="form-label">Enter You Mobile/Email/BookingId</label>
                        <input id="booking_number" type="booking_number" class="form-control @error('booking_number') is-invalid @enderror" name="booking_number"
                          value="{{ session()->get('booking_number') }}"
                          required autocomplete="booking_number"
                          autofocus>
                         @error('booking_number')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror

                    </div>
                    <div class="col-12 text-center">
                      <button class="btn btn-primary" type="submit">Submit</button>
                    </div>

                    @if(session()->has('book_seat'))

                      <a href="{{ route('book.show') }}">Book again</a>
                    @endif

                  </form>

                  @if(session()->has('enable'))
                  <form class="row g-3 needs-validation mt-5" action="{{ route('booking.confirm-spot.otp.post') }}" method="post">

                     @csrf
                     @if(session()->has('booking_number'))
                     <input type="hidden" name="booking_number" value="{{ session()->get('booking_number') }}">
                     @endif

                    <div class="col-12">
                         <label for="yourPassword" class="form-label">Enter You OTP (One Time Password)</label>
                        <input id="otp" type="number" class="form-control
                        @error('otp') is-invalid @enderror"
                           name="otp"
                          value="{{ old('otp') }}"
                          required autocomplete="otp"
                          autofocus>
                         @error('otp')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror

                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Verify and Confirm</button>
                    </div>

                  </form>




                  @endif


                </div>
              </div>

              <div class="credits">
              </div>

            </div>
          </div>
        </div>

      </section>
      <script>
        document.title = "kahayFaqeer.org|Confirm Spot";
      </script>





@endsection
