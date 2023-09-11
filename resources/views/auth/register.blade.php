@extends('layouts.guest')

@section('content')


<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logoo  d-flex align-items-center wuto">
                  <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
                  <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? ''}}</span> -->
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>



                  <form class="row g-3 needs-validation" action="{{ route('post-signup') }}" method="post">
                     @csrf

                    <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Name</label>
                        <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                         @error('name')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     
                    </div>

                     <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                         @error('email')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     
                    </div>
                    <div class="col-12"> 
                            <label for="yourPassword" class="form-label">Your Domain</label>
                        <div class="input-group mb-3">
                          <input type="text" class="form-control" placeholder="Domain" aria-label="Domain" aria-describedby="basic-addon2" name="domain" required>
                          <span class="input-group-text" id="basic-addon2">.{{ request()->getHost() }}</span>
                        </div>
                    </div>

                    <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autocomplete="password" autofocus>
                         @error('password')
                         <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     
                    </div>

                    <div class="col-12"> 
                         <label for="yourPassword" class="form-label">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" value="{{ old('password') }}" required autocomplete="password_confirmation" autofocus>
                        
                    </div> 
                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required="">
                        <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div>

                    
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
                    </div>
                    <!-- <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p>
                    </div> -->
                  </form>

                </div>
              </div>

              <div class="credits"> 
              </div>

            </div>
          </div>
        </div>

      </section>

 
@endsection
