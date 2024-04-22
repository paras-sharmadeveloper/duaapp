@extends('layouts.guest')

@section('content')

<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="{{ route('home') }}" class="logoo  d-flex align-items-center wuto">
                  <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
                  <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? ''}}</span> -->
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
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




                  <form class="row g-3 needs-validation" action="{{ route('post-login') }}" method="post">
                     @csrf

                    <div class="col-12">
                         <label for="yourPassword" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                          value="{{ old('email') }}"
                          required autocomplete="email"
                          autofocus>
                         @error('email')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror

                    </div>


                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>

                    </div>


                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    {{-- <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="{{ route('register') }}">Create an account</a></p>
                    </div> --}}
                  </form>

                </div>
              </div>

              <div class="credits">
              </div>

            </div>
          </div>
        </div>

      </section>


     <div class="modal fade" id="basicModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Resent Verification Email</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                     <form class="row g-3 needs-validation" novalidate="" action="{{ route('user.resend') }}" method="post">
                      @csrf
                        <div class="col-12">
                          <label for="yourEmail" class="form-label">Your Email</label>
                          <input type="email" name="email" class="form-control" id="yourEmail" required  value="{{ old('email') }}" >
                          <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                        </div>
                      <button type="submit" class="btn btn-primary">Resent code</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                     </form>
                    </div>
                  </div>
                </div>
  </div><!-- End Basic Modal-->

  <script>
    document.title = "kahayFaqeer.org| App Login";
  </script>
@endsection
