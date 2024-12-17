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
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Pick Action</p>
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

                    <div class="row">
                          <div class="d-flex justify-content-between py-4">
                            <form method="get" action="{{ route('login') }}">
                               <button type="submit" class="btn btn-outline-primary">Super Admin</button>
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#basicModal">
                              CustomerLogin
                            </button>
                            </form>
                         </div>
                    </div>

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
                      <h5 class="modal-title">Enter your domain</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                    <div class="col-12">
                            <label for="yourPassword" class="form-label">Your Domain</label>
                        <div class="input-group mb-3">
                          <input type="text" class="form-control" placeholder="Domain" aria-label="Domain" aria-describedby="basic-addon2" name="domain" required id="check-domain">
                          <span class="input-group-text" id="basic-addon2">.{{ request()->getHost() }}</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" id="send-to-login">Send to MyLogin</button>
                    </div>



                    </div>
                  </div>
                </div>
  </div>
  <!-- End Basic Modal-->


@endsection
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>


<script type="text/javascript">

    $(document).ready(function() {

      $("#send-to-login").click(function(){


        var domain = $("#check-domain").val();

        if(domain){
          window.open(location.protocol+'//'+domain+"."+location.host+'/login')
        }else{
          alert('enter domain')
        }


      });

    });


</script>
