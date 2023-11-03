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
                    <h5 class="card-title text-center pb-0 fs-4">Are You Sure You want to Cancle Your Spot ?</h5>
                    <p class="text-center small">if yore really want to cancle please enter your mobile number register with us ?</p>
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

                     
                        

                  <form class="row g-3 needs-validation " action="{{ route('book.cancle',['id' => $vistor->booking_uniqueid]) }}" method="post">
                    <p class="text-center small">Your Mobile number Register with us {{ str_repeat('*', strlen($vistor->phone) - 4) . substr($vistor->phone, -4) }}                    </p>
                     @csrf
                     

                    <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Enter You Mobile Number Here</label>
                        <input id="phone" type="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" 
                          value="{{ (session()->has('booking_number')) ? session()->get('booking_number') : ''  }}" 
                          required autocomplete="phone" 
                          autofocus>
                         @error('phone')
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
                  <form class="row g-3 needs-validation mt-5" action="{{ route('book.cancle.otp',['id' => $vistor->booking_uniqueid ]) }}" method="post">
                    
                     @csrf
                     <input type="hidden" name="booking_number" value="{{ session()->get('booking_number') }}">
                    <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Enter You OTP (One Time Password)</label>
                        <input id="otp" type="otp" class="form-control @error('otp') is-invalid @enderror" name="otp" 
                          value="{{ old('otp') }}" 
                          required autocomplete="otp" 
                          autofocus>
                         @error('otp')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Verify and Cancle</button>
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
        document.title = "KahayFaqeer.com| Cancle Booking";
      </script>
     

 
@endsection
