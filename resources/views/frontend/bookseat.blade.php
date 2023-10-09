@extends('layouts.guest')
@section('content') 
<style>   
    h1,h2,h3,h4,h5,h6,p,li{
      color: #ffffff; 
    }
     
        body{background-color:rgb(29, 29, 29);font-family:Karla,sans-serif}.select2-container .select2-selection--single{height:38px}.main-content .wizard-form .progressbar-list::before{content:" ";background-color:#9b9b9b;border:10px solid #fff;border-radius:50%;display:block;width:30px;height:30px;margin:9px auto;box-shadow:1px 1px 3px #606060;transition:none}.main-content .wizard-form .progressbar-list::after{content:"";background-color:#9b9b9b;padding:0;position:absolute;top:14px;left:-50%;width:100%;height:2px;margin:9px auto;z-index:-1;transition:.8s}.main-content .wizard-form .progressbar-list.active::after{background-color:#763cb0}.main-content .wizard-form .progressbar-list:first-child::after{content:none}.main-content .wizard-form .progressbar-list.active::before{font-family:"Font Awesome 5 free";content:"\f00c";font-size:11px;font-weight:600;color:#fff;padding:6px;background-color:#763cb0;border:1px solid #763cb0;box-shadow:0 0 0 7.5px rgb(118 60 176 / 11%)}.progressbar-list{color:#6f787d}.active{color:#000}.card img{width:40px;margin:auto}.card{border:3px solid rgb(145 145 145);cursor:pointer}.active-card{color:#763cb0;font-weight:700;border:6px solid #15d92b}.form-check-input:focus{box-shadow:none}.bg-color-info{background-color:#00d69f}.border-color{border-color:#ececec}.btn{padding:16px 30px}.back-to-wizard{transform:translate(-50%,-139%)!important}.bg-success-color{background-color:#87d185}.bg-success-color:focus{box-shadow:0 0 0 .25rem rgb(55 197 20 / 25%)}.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:500px;height:500px;overflow:overlay}div#slot-listing h1{width:100%}button.btn:hover{color:#000!important;background-color:grey}.card-title{padding:10px 0 4px;font-size:14px;font-weight:500;color:#012970;font-family:Poppins,sans-serif}.danger,.success{text-align:center;font-size:16px}.card-body{padding:0 17px 0 20px}#selfie-image,video#video{height:250px;width:300px}div#captured-image{margin-bottom:15px}.loader{border:5px solid #3498db;border-top:5px solid transparent;border-radius:50%;width:40px;height:40px;animation:1s linear infinite spin}.loader-main{display:flex;justify-content:center;margin-top:5px}.success{color:green;font-weight:900}.danger,.error{color:red;font-weight:bold}div#error{margin:20px 0}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        @media (max-width:767px){
            .head label {
                font-size: 15px !important; 
                color:#fff !important;
            }
            div#loader {text-align: center !important;}
            .head {  display: inherit !important;margin-top: 10px;}
            span.select2.select2-container.select2-container--default{width:100%!important;flex:auto!important}.col{flex-shrink:0!important;flex:auto}.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:375px}.selfie{text-align:center}.p-4{padding:.5rem!important}.card{margin-bottom:20px}.logoo img{height:50px;width:50px}.mt-4{margin-top:.5rem!important}.error.country_code{font-size:14px;bottom:-20px}.row .loader-img{margin:17px!important}
        .thripist-section img {
            height: 30% !important;
            width: 30% !important;
            border-radius: 20%;
        }
        .col-lg-6 {
            flex: 0 0 auto;
            width: 50% !important;
        }
        .otp-btn{
            text-align: center;
            margin: 12px 0px; 
        }
        div#opt-form-confirm {
            text-align: center;
        }
        .cusmhtn {font-size: 12px;}

        }
        .btn.next{
            background: #f9d20a !important; 
        }
        @media (min-width:1024px){.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:380px}.error.country_code{bottom:-35px}}figcaption{font-size:10px}
        .thripist-section img{height:50%;width:50%;border-radius:20%}

        .loader-img{height:64px;width:64px!important}#progressBar .w-25{width:14%!important}.row .loader-img{margin:auto}
        /* .col-lg-6 {
            flex: 0 0 auto;
            width: 20%;
        } */
        .select2-container{
            width: 100%;
        }
        .select2-container {
    width: 100% !important;
}

        /* css loader start  */
        .lds-spinner {
  color: official;
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-spinner div {
  transform-origin: 40px 40px;
  animation: lds-spinner 1.2s linear infinite;
}
.lds-spinner div:after {
  content: " ";
  display: block;
  position: absolute;
  top: 3px;
  left: 37px;
  width: 6px;
  height: 18px;
  border-radius: 20%;
  background: #ffffff;
}
.lds-spinner div:nth-child(1) {
  transform: rotate(0deg);
  animation-delay: -1.1s;
}
.lds-spinner div:nth-child(2) {
  transform: rotate(30deg);
  animation-delay: -1s;
}
.lds-spinner div:nth-child(3) {
  transform: rotate(60deg);
  animation-delay: -0.9s;
}
.lds-spinner div:nth-child(4) {
  transform: rotate(90deg);
  animation-delay: -0.8s;
}
.lds-spinner div:nth-child(5) {
  transform: rotate(120deg);
  animation-delay: -0.7s;
}
.lds-spinner div:nth-child(6) {
  transform: rotate(150deg);
  animation-delay: -0.6s;
}
.lds-spinner div:nth-child(7) {
  transform: rotate(180deg);
  animation-delay: -0.5s;
}
.lds-spinner div:nth-child(8) {
  transform: rotate(210deg);
  animation-delay: -0.4s;
}
.lds-spinner div:nth-child(9) {
  transform: rotate(240deg);
  animation-delay: -0.3s;
}
.lds-spinner div:nth-child(10) {
  transform: rotate(270deg);
  animation-delay: -0.2s;
}
.lds-spinner div:nth-child(11) {
  transform: rotate(300deg);
  animation-delay: -0.1s;
}
.lds-spinner div:nth-child(12) {
  transform: rotate(330deg);
  animation-delay: 0s;
}
@keyframes lds-spinner {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}
.btn-cst {
    padding: 6px 10px;
}

.invalid-slot {
    border: 2px solid red;
}


.action-wrapper {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}
.action-wrapper p {
  margin: 0;
}
.action-wrapper select {
  padding: 5px;
  margin-left: 10px;
}

.wrapper {
  padding: 25px 35px; 
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  background: #fff;
  box-shadow: 0px 3px 10px 3px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
}

.wrapper ul {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-left: 0;
  margin-top: 4.7px;
  margin-bottom: 0;
  list-style-type: none;
}
.wrapper ul li {
  position: relative;
  margin-top: 10px;
  font-size: 12px;
  flex-basis: 0;
  flex-grow: 1;
  max-width: 100%;
  text-align: center;
  color: #7f8995;
}
.wrapper  ul li.d-none {
  display: none;
}
.wrapper  ul li:last-child:after {
  display: none;
}
.wrapper ul li:before {
  content: "";
  position: absolute;
  top: -20px;
  left: 47%;
  z-index: 1;
  height: 8px;
  width: 8px;
  background: #19d184;
  border-radius: 50%;
  box-shadow: 0 0 0 2px white;
}
.wrapper ul li:after {
  content: "";
  position: absolute;
  top: -17px;
  left: 50%;
  width: 100%;
  height: 2px;
  background: #19d184;
}
.wrapper ul li.active ~ li:before {
  background: #dde2e5;
}
.wrapper  ul li.active ~ li:after {
  background: rgba(221, 226, 229, 0.4);
}
.wrapper  ul li.active:before {
  background-color: #198fd1;
  box-shadow: 0 0 0 3px rgba(25, 143, 209, 0.2);
}
.wrapper ul li.active:after {
  background: rgba(221, 226, 229, 0.4);
}
.head {
    display: flex;
    justify-content: space-between;
    color: white;
}
.head label {
    font-size: 26px;
    font-weight: 700;
}
.select2-results__options li {
    color: #000 !important;
}
#sendOtp label{
    color: #fff;
}
        /* css loader ends */
</style>
    <!-- section -->
    <section>
        <!-- container -->
        <div class="container">
            <!-- main content -->
            <div class="main-content">

                <div class="d-flex justify-content-center py-4">
                    <a href="index.html" class="logoo  d-flex align-items-center wuto">
                        {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                        <img src="https://kahayfaqeer.org/assets/kahe-faqeer-white-1.png" alt="">
                       
                        <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
                    </a>
                </div>


                <div class="row justify-content-center pt-0 p-4" id="wizardRow">
                    
                    <!-- col -->
                    <div class="col-md-12 text-center wizard-form">
                        <div class="wrapper">
                            <ul class="status-line" id="progress-bar">
                              <li class="active">Sahib-e-Dua</li>
                              <li>Meeting Type</li>
                              <li>Country</li>
                              <li>City</li>
                              <li>Date</li>
                              <li>Slot</li>
                              <li>Finish</li> 
                            </ul>
                          </div>

                      
                        <!-- wizard -->
                        {{-- <div class="wizard-form py-4 my-2 d-none">
                            <!-- ul -->
                            <ul id="progressBar" class="progressbar px-lg-5 px-0">
                                <li id="progressList-0"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list active">
                                    Step 1</li>
                                <li id="progressList-1"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 2</li>
                                <li id="progressList-2"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 3</li>
                                <li id="progressList-3"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 4</li>
                                <li id="progressList-4"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 5</li>
                                <li id="progressList-5"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 6</li>
                                <li id="progressList-6"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Done</li>

                                    <div id="loader" style="display: none">
                                    <div class="lds-spinner">
                                        <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                    </div>
                                    </div>

                                  
                                  
                            </ul> 
                            <!-- /ul -->
                        </div> --}}
                        
                        <!-- /wizard -->
                    </div>
                </div>
 

                <div class="row justify-content-center" id="cardSection">
                       
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Sahib-e-Dua</h3>
                            <label></label>
                        </div>
                        
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom">
                            @foreach ($therapists as $therapist)
                                <div class="col-lg-3 col-md-4">
                                    <div class="card text-center h-60 py-2 shadow-sm thripist-section"
                                        data-id="{{ $therapist->id }}">
                                        @if(!empty($therapist->profile_pic))
                                        <img src="{{ env('AWS_GENERAL_PATH').'images/'.$therapist->profile_pic }}" alt="Profile">
                                        @else 
                                        <img src="{{ asset('assets/theme/img/avatar.png') }}">
                                        @endif
                                        {{-- <i class="fas fa-building card-img-top mx-auto img-light fs-1 pb-1"></i> --}}
                                        <div class="card-body px-0">
                                            <h5 class="card-title title-binding">{{ $therapist->name }}</h5>
                                            <p class="card-text">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info country-next">Next</button>
                    </div>
                    <!-- col --> 
                </div>

                <div class="row justify-content-center  form-business">
                   
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Type</h3>
                            <label></label>
                        </div> 
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="type-listing">
                            <div id="loader" style="display: none">
                                <div class="lds-spinner">
                                    <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info country-next">Next</button>
                    </div> 
                </div>
 
                <!-- row -->
                <div class="row justify-content-center  form-business" >
                   
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Country</h3>
                            <label></label>
                        </div>
                         
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="country-listing">
                              <div id="loader" style="display: none">
                                    <div class="lds-spinner">
                                        <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                    </div>
                                    </div>
                        </div>
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info country-next">Next</button>
                    </div> 
                </div>
                <div class="row justify-content-center form-business">
                    <!-- col -->
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select City</h3>
                            <label></label>
                        </div> 
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom" id="city-listing">
                              <div id="loader" style="display: none">
                                    <div class="lds-spinner">
                                        <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                    </div>
                                    </div>
                        </div> 
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /col -->

                <div class="row justify-content-center form-business">
                    <!-- col -->
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Event Date</h3>
                            <label></label>
                        </div>  
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom" id="date-listing">
                              <div id="loader" style="display: none">
                                    <div class="lds-spinner">
                                        <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                    </div>
                                    </div>
                        </div>
                        <!-- /cards -->
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
 
                <!-- row -->
                <div class="row justify-content-center form-business sloting-main">
                    <!-- col -->
                    <div class="col-lg-12 col-md-12 slot-in">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Event Slot</h3>
                            <label></label>
                        </div>   
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-2 row-cols-lg-5 g-4 pb-0 border-bottom sloting-inner" id="slot-listing">
                              <div id="loader" style="display: none">
                                    <div class="lds-spinner">
                                        <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                                    </div>
                                    </div>
                        </div>
                        <!-- /cards -->
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm" id="slot-next">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center py-5 form-business">

                    <div class="head mb-4">
                        {{-- <h3 class="fw-bold text-center">Final</h3> --}}
                        <label></label>
                    </div>
                    <!-- col -->
                    {{-- <div class="col-lg-12 col-md-12" id="successMessage">
                        <header class="site-header" id="header">
                            <h1 class="site-header__title" data-lead-id="site-header-title">THANK YOU!</h1>
                        </header>
                        <div class="main-content">
                            <i class="fa fa-check main-content__checkmark" id="checkmark"></i>
                            <p class="main-content__body" data-lead-id="main-content-body">Thanks a bunch for filling that out. It means a lot to us, just like you do! We really appreciate you giving us a moment of your time today. Thanks for being you.</p>
                        </div>
                        
                    </div> --}}
                    <!-- /col -->
                    <!-- col -->
                    <div class="col-lg-12 col-md-12" id="successForm">
                        <div class="mb-5">
                            <!-- Final step -->
                            <div class="alert alert-primary text-center d-none" role="alert">
                                {{-- <h5 class="p-4 cusmhtn">Finally We are going to submit your information if you want to continue
                                    that
                                    please click on the finish button to finish up your Working process.</h5> --}}
                            </div>

                            <form action="{{ route('booking.submit') }}" method="post" id="booking-form"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="slot_id" id="slot_id_booked" value="">
                                <div class="row g-3 mb-3">
                                    <div class="col col-md-6">
                                        <input type="text" class="form-control" name="fname" placeholder="Enter your first name"
                                            aria-label="First name">
                                    </div>
                                    <div class="col col-md-6">
                                        <input type="text" class="form-control" name="lname" placeholder="Enter your last name"
                                            aria-label="Last name">
                                    </div>
                                    
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col col-md-12">
                                        <input type="email" class="form-control" name="email"
                                            placeholder="Eg:test@example.com" aria-label="Email">
                                    </div> 
                                </div>
                                

                                <div class="row g-3 mb-3"> 
                                        <div class="col col-lg-5  col-md-5">

                                            <select id="country_code" name="country_code" class="js-states form-control">
                                                <option value="">select</option>
                                                @foreach ($countryList as $country)
                                                    <option value="{{ $country->phonecode }}"> {{ $country->name }}
                                                        {{ '(+' . $country->phonecode . ')' }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                       
                                        <div class="col col-lg-7 col-md-12" id="mobile-number">
                                            <input type="number" class="form-control" id="mobile" name="mobile"
                                                placeholder="Eg:8884445555" aria-label="Mobile">
                                                <p> </p>
                                        </div>
                                        <div class="col col-lg-2 col-md-12" id="opt-form-confirm" style="display: none">
                                            <button type="button" id="sendOtp" class="btn-cst  btn btn-primary testbtn" type="button" data-loading="Sending OTP" data-success="Success" data-default="Send OTP">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                                </span>
                                                <label> Sent OTP</label>
                                              </button>
                                              <p></p>
          
                                        </div> 
 
                                </div>
                                <div id="opt-form" style="display: none">
                                <div class="row mt-2">
                                    <div class="col col-lg-5 col-md-12  col-sm-12">
                                        <input type="text" class="form-control"  name="otp" id="otp" placeholder="Enter OTP">
                                        <p></p>
                                    </div>
                                    <div class="col col-lg-7 col-md-12  col-sm-12 otp-btn">
                                        <button type="button"  id="submit-otp"  class="btn-cst  btn btn-primary testbtn" type="button" 
                                        data-loading="Verifying OTP" 
                                        data-success="Success" 
                                        data-default="Submit">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                            </span>
                                            <label> Submit</label>
                                          </button> 
                                        
                                    </div>
                                    
                                </div>
                            </div>

                                {{-- <div class="row g-3">
                                    <div class="col col-md-12">
                                        <textarea name="user_question" id="" cols="30" rows="3"
                                            placeholder="Put some line of your query" class="form-control"></textarea>

                                    </div>

                                </div> --}}
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_whatsapp"
                                        id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{-- I agree to the terms and conditions --}}
                                        This number is on whatsapp ?
                                    </label>
                                </div>

                                <div class="form-group row mt-3 selfie">
                                    {{-- <label for="selfie" class="col-md-4 col-form-label text-md-right">{{ __('Selfie') }}</label> --}}

                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-outline-success btn-cst" type="button"   id="start-camera">Take Selfie</button>
                                       
                                        <!-- Add a camera view area -->
                                        <div id="camera-view" style="display: none;">
                                            <video id="video" autoplay playsinline></video>
                                        </div>
                                        <!-- Display the captured image -->
                                        <div id="captured-image" style="display: none;">
                                           
                                            <img id="selfie-image" src="" alt="Captured Selfie"> 
                                            <div id="error"></div> 
                                        </div>
 
                                      
                                        <input type="hidden" id="selfie" name="selfie" required>
 
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="loader-main" id="loader-main" style="display: none">
                                        <div class="loader mb-3"></div>
                                    
                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-evenly">
                                      
                                        <button class="btn btn-outline-primary btn-cst" type="button" style="display: none;"   id="capture-selfie">Capture</button>
                                    
                                   </div>
                                    <div class="col-lg-6 d-flex justify-content-evenly">
                                       
                                        <button class="btn btn-outline-info mr-2 btn-cst" type="button" style="display: none;"   id="restart-camera">Restart camera</button>
                                        
                                   </div>
                                   
                               </div>
                                <!-- /Final step -->
                        </div> 

                       
                        

                       

                        
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back rounded-3">Back</button>

                        <button type="submit"  id="submitBtn"  class="btn text-white float-end submit-button rounded-3 bg-color-info" type="submit" 
                        data-loading="Submitting..." 
                        data-success="Done" 
                        data-default="Finish">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                            </span>
                            <b> Finish</b>
                        </button> 
                          
                        </form>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
            </div>
            <div id="remeber-steps" class="d-none">
                <input type="hidden" name="remeber-steps-app" id="remeber-steps-app" data-step="1">
            </div>
            <!-- /main content -->
        </div>
        <!-- /container -->
    </section>


    <!-- /section -->
@endsection

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>

        
       
 
          $(".form-business").hide();
        $("#country_code").select2({
            placeholder: "Select country",
            allowClear: true
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(document).on("click",".checkSlot",function(){
            $("#slot-next").hide();
            var id = $(this).attr('data-id');
            
            $.ajax({
                    url: "{{ route('check-available')  }}", // Update the URL to your Laravel endpoint
                    method: 'POST',
                    data: { id: id , _token: "{{ csrf_token() }}"},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status ==true){
                            $("#slot-next").show();
                        }
                       
                    },
                    error: function(error) {
                         
                        $(this).addClass('invalid-slot').fadeOut(1500); 
                        $("#slot-next").hide();
                    }
                });

        });
        $(document).ready(function() {
            
            $(".next").on({
                click: function() {
                    
                   
                    
                    var getValue = $(this).parents(".row").find(".card").hasClass("active-card");

                   
                    if (getValue) {
                       
                        var oldTitle = $("#remeber-steps-app").val(); 
                       
                        var title = $(this).parents(".row").find(".active-card").find(".title-binding").text();

                        if(oldTitle == ''){
                            oldTitle = title; 
                        }else{
                            oldTitle+= " > " + title; 
                        } 

                        $(this).parents(".row").find(".head>label").text(oldTitle); 
                        $("#remeber-steps-app").val(oldTitle); 
                        
                        $(this).parents('.justify-content-center').find('.head>label').text(oldTitle); 





                        var oldTitle = $("#remeber-steps-app").val();
                        
                        $("#progress-bar").find(".active").next().addClass("active").prev().removeClass('active');
                        $(this).parents(".row").find(".alertBox").addClass("d-none")
                        
                        $("#loader").show();
                        $(this).parents(".row").fadeOut("slow", function() {
                          $(this).next(".row").fadeIn(); 
                          $(this).next(".row").find('.head>label').text(oldTitle)
                          $("#loader").hide();
                         });
                      
                        

                    } else {
                       

                        $("#loader").hide();
                        $(this).parents(".row").find(".alertBox").removeClass("d-none").text("Please select any card , only then you can move further!")
                         
                        // $("alertBox").removeClass("d-none").find("div").text("Please select any card , only then you can move further!");
                    }
                }
            });
            // back button
            $(".back").on({
                click: function() { 
                    
                    $("#progress-bar").find(".active").removeClass('active').prev().addClass('active')

                    var currentTitle = $('#remeber-steps-app').val();
                       currentTitle = currentTitle.split(' > ');
                       currentTitle.pop(); 

                       var newString = currentTitle.join(' > ');
                       console.log("newString",newString)
                       $('#remeber-steps-app').val(newString)
                   
                   // $(this).next(".row").show();
                    $(this).parents(".row").fadeOut("slow", function() {
                        $("#loader").hide();
                        $(this).prev(".row").fadeIn();
                        $(this).prev(".row").find(".head").find("label").text(newString)
                    });
                }
            });
            //finish button


            // $(document).on("click", ".submit-button", function() {
            //     $("#wizardRow").fadeOut(300);
            //     $(this).parents(".row").children("#successForm").fadeOut(300);
            //     $(this).parents(".row").children("#successMessage").fadeIn(3000);
            // })
            //Active card on click function

            $(document).on("click", ".card", function() {
               
                $(this).toggleClass("active-card");
                var cardId = $(this).attr('data-id');

                if ($(this).hasClass('thripist-section')) { 
                    getAjax(cardId, 'get_type')
                    // getAjax(cardId, 'get_country')
                }else if ($(this).hasClass('type-selection')) { 
                    getAjax(cardId, 'get_country') 
               }else if ($(this).hasClass('city-selection')) { 
                  
                     getAjax(cardId, 'get_city')
                } else if ($(this).hasClass('date-selection')) { 
                    getAjax(cardId, 'get_date')
                  //  $("#slot_id_booked").val(cardId);
                } 
                else if ($(this).hasClass('slot-selection')) { 
                    getAjax(cardId, 'get_slots')
                    // $("#slot_id_booked").val(cardId);
                }else if ($(this).hasClass('slot-capture')) { 
                    // getAjax(cardId, 'get_slots')
                     $("#slot_id_booked").val(cardId);
                }

                

                $(this).parent(".col").siblings().children(".card").removeClass("active-card");
            })
            //back to wizard
            $(".back-to-wizard").on({
                click: function() {
                    location.reload(true);
                }
            });
        });

        function getAjax(id, type) { 
           

            $.ajax({
                type: 'POST',
                url: "{{ route('booking.ajax') }}",
                data: {
                    "id": id,
                    "type": type
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $("#loader").hide();
                    if (type == 'get_type') { 
                        var typed = '';
                       
                        $.each(response.type, function(key, item) {

                            var meetingType = 'Online';
                            if(item.name == 'on-site'){
                                meetingType = 'Physical';
                            }
                            typed += `<div class="col col-lg-3 col-md-7">
                                <div class="card text-center h-60 py-2 shadow-sm type-selection" 
                                 data-id="${item.venue_address_id}" 
                                 data-type="${item.name}"> 
                                    <img src="${item.flag_path}" alt="Flag Image"> 
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${meetingType}</h5>
                                    </div>
                                </div>
                            </div>`;
                        })
                        $("#type-listing").html(typed); 
 
                    }


                    if (type == 'get_country') {
                        var country = '';
                        $.each(response.country, function(key, item) {
                            var meetingType = 'Online';
                            if(item.type == 'on-site'){
                                meetingType = item.name;
                            }
                            country += `<div class="col col-lg-3 col-md-7">
                            <div class="card text-center h-60 py-2 shadow-sm city-selection" data-id="${item.id}">
                                <img src="${item.flag_path}" alt="Flag Image"> 
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${meetingType}</h5>
                                   
                                </div>
                            </div>
                        </div>`;
                        })
                        $("#country-listing").html(country); 

                    }
                    if (type == 'get_city') { 
                       
                        var city = '';
                        $.each(response.city, function(key, item) {
                            var meetingType = 'Online';
                            if(item.type == 'on-site'){
                                meetingType = item.name;
                            }
                            city += `<div class="col col-lg-3 col-md-7 country-enable-n country-enable-${item.id}">
                                <div class="card text-center h-60 py-2 shadow-sm date-selection" data-id="${item.venue_address_id}">
                                    <img src="${item.flag_path}" alt="Flag Image"> 
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${meetingType}</h5>
                                        
                                    </div>
                                </div>
                            </div>`;
                       })
                       $("#city-listing").html(city); 

                    }
                    if (type == 'get_date') {  

                        var dAte = '';
                        $.each(response.date, function(key, item) { 
                            
                            dAte += `<div class="col col-lg-3 col-md-7 date-enable-n date-enable-${item.venue_address_id}">
                                <div class="card text-center h-60 py-2 shadow-sm slot-selection" data-id="${item.venue_address_id}">
                                    <img src="${item.flag_path}" alt="Flag Image"> 
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${convertDateToCustomFormat(item.venue_date)}</h5>  
                                    </div>
                                </div>
                            </div>`;
                        })
                        $("#date-listing").html(dAte);
                    } 
                    
                    if (type == 'get_slots') {
                        var html = '';
                        if (response.status) {
                            $.each(response.slots, function(key, item) {
                                html += `<div class="col col-lg-3 col-md-7">
                                <div class="card text-center h-10 py-0 shadow-sm slot-capture checkSlot" data-id="${item.id}">
                                    
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${convertTimeTo12HourFormat(item.slot_time)}</h5>
                                        
                                    </div>
                                </div>
                                </div>`;
                            });
                            $("#slot-listing").html(html);
                            $(".confirm").show();
                            $(".back").show();
                        } else {
                            $("#slot-listing").html("<h1>" + response.message + "</h1>");
                            $(".confirm").hide();
                            $(".back").show();
                        }


                    }


                }
            });




        }
    </script>
    <script>
     
        $(document).ready(function() {
            // Add an event listener to the form's submit event
            $('#booking-form').submit(function(event) {
                $this = $("#submitBtn"); 
                var loadingText = $this.attr('data-loading');
                var successText = $this.attr('data-success');
                var defaultText = $this.attr('data-default');

                $this.find('span').show()
                $this.find('b').text(loadingText)
                event.preventDefault(); // Prevent the default form submission
                $("#loader").show();
                // Serialize the form data
                var formData = $(this).serialize();

                // Perform the AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Get the form's action URL
                    type: $(this).attr('method'), // Get the form's HTTP method (POST in this case)
                    data: formData, // Use the serialized form data
                    success: function(response) {
                        $this.find('span').show()
                        $this.find('b').text(successText)
                        setTimeout(() => {
                            $this.find('b').text(defaultText)
                            $("#wizardRow").fadeOut(300);
                        $("#successForm").fadeOut(300);
                        },1000);
                        // 'thankyou-page
                         
                        $("#loader").hide();
                        window.location.href = '/booking/thankyou/'+response.bookingId; 
                        
                    },
                    error: function(error) {
                        
                        if (error.responseJSON && error.responseJSON.errors) {

                            $this.find('b').text(defaultText)
                            $this.find('span').hide()
                            if(error.responseJSON.status== false){
                                 
                                $this.find('b').text('Opps Error..')
                                setTimeout(() => {
                                    $this.find('b').text(defaultText)
                                }, 2000);
                            }
                            var errors = error.responseJSON.errors;
                            $("#errors").html(error.responseJSON.message);

                            // Clear any existing error messages
                            $('.alert-danger').remove();
                                $(".error").remove(); 
                            $.each(errors, function(field, messages) {
                                
                                var inputElement = $('[name="' + field + '"]');
                                inputElement.addClass('is-invalid');
                                if(field == 'country_code'){
                                    inputElement.before('<div class="error '+field+'">' + messages.join('<br>') + '</div>');
                                }else{
                                    inputElement.after('<div class="error '+field+'">' + messages.join('<br>') + '</div>');
                                }
                               
                               
                            });
                        }
                        // Handle other types of errors here if needed
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var video = document.getElementById('video');
            var startCameraButton = $('#start-camera');
            var captureSelfieButton = $('#capture-selfie');
            var restartCameraButton = $('#restart-camera');
            var capturedImageDiv = $('#captured-image');
            var selfieImage = $('#selfie-image');
            var selfieInput = $('#selfie');
            // Add an event listener to the "Start Camera" button

            function startCamera() {
                $("#camera-view").show();
                // Access the camera and display the feed in the video element
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(function(stream) {
                        video.srcObject = stream;

                        // When the stream is loaded, start playing the video
                        video.onloadedmetadata = function(e) {
                            video.play();
                            // Show the "Capture Selfie" button and hide the "Start Camera" button
                            startCameraButton.hide();
                            captureSelfieButton.show();
                            restartCameraButton.show();
                        };
                    })
                    .catch(function(error) {
                        console.error('Error accessing camera:', error);
                    });
            }

            startCameraButton.on('click', function() {
                startCamera();
            });

            restartCameraButton.on('click', function() {
                // Hide the captured image and show the camera view
                capturedImageDiv.hide();
                video.srcObject = null;
                startCamera();
            });

            // Add an event listener to the "Capture Selfie" button
            captureSelfieButton.on('click', function() { 
                // Create a canvas to capture the current frame
                var canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                var context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert the captured frame to base64 and set it in the hidden input field
                var selfieDataUrl = canvas.toDataURL('image/jpeg');
                selfieInput.val(selfieDataUrl);


                // Stop the video stream and hide the camera view
                var stream = video.srcObject;
                if (stream) {
                    var tracks = stream.getTracks();
                    tracks.forEach(function(track) {
                        track.stop();
                    });
                }
                video.srcObject = null;
                $("#camera-view").hide();
                capturedImageDiv.show();

                selfieImage.attr('src', selfieDataUrl);

                // Show the "Restart Camera" button and hide the "Capture Selfie" button
                captureSelfieButton.hide();
                restartCameraButton.show();
                restartCameraButton.prop('disabled', false);
                $("#loader-main").show()
                $("#submitBtn").hide();
                $.ajax({
                    url: '/detect-liveness', // Update the URL to your Laravel endpoint
                    method: 'POST',
                    data: {
                        image: selfieDataUrl, // Send the base64-encoded image data
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#loader-main").hide()
                        $("#submitBtn").show(); 

                        $("#submitBtn").show();
                        $("#error").removeClass('danger');
                        $("#error").addClass('success').text("Perfect. You can proceed");
                     


                    },
                    error: function(error) {
                        $("#loader-main").hide()
                        // Handle errors
                        if (error.responseJSON.status == false) {
                            $("#error").removeClass('success');
                            $("#error").addClass('danger').text(
                                "We are unable to detect a face. It look like this is something object or other. Please retry again."
                            )
                            $("#submitBtn").hide();
                        }

                    }
                });
            });

        });
    </script>

<script>
    $(document).ready(function() {
       $('#mobile').on('input', function() {
           // Get the value of the phone input
           let phoneNumber = $(this).val();

           // Remove any non-digit characters (e.g., spaces, dashes)
           phoneNumber = phoneNumber.replace(/\D/g, '');

           // Check if the phone number has reached 10 digits
           if (phoneNumber.length === 10) {
               $("#submitBtn").hide();
               $("#opt-form-confirm").fadeIn(500);
               $("#mobile-number").removeClass('col-lg-7').addClass('col-lg-5');
           }else{
               $("#submitBtn").show();
               $("#opt-form-confirm").fadeOut(500);
               $("#mobile-number").removeClass('col-lg-5').addClass('col-lg-7');
           }
       });
       $("#sendOtp").click(function(){

             $this= $(this); 
 
            var loadingText = $this.attr('data-loading');
            var successText = $this.attr('data-success');
            var defaultText = $this.attr('data-default');

            $this.find('span').show()
            $this.find('label').text(loadingText)
           
            
          
           $.ajax({
               url: "{{ route('send-otp') }}",
               type: 'POST',
               data: {  
                   country_code : $("#country_code").val(),
                   mobile : $("#mobile").val(),

               },
               success: function(response) {
                $this.find('label').text(successText)
                $this.find('span').hide()
                // setTimeout(() => {
                //     $this.find('label').text(defaultText) 
                // }, 2500);
  
                $("#opt-form").show();  
                $("#submitBtn").hide(); 
                $("#mobile-number").find('p').addClass('text-success').text(response.message);
                $this.find('label').text("Resend")
                                   
               },
               error: function(xhr) {
               
                $this.find('span').hide()
                $this.find('label').text(defaultText)
                $("#mobile-number").find('p').addClass('text-danger').text(xhr.responseJSON.message);
                    
               }
           });
       })

       $("#submit-otp").click(function(){
        $("#loader-otp2").show();
        
           $("#opt-form").show(); 
           
           $("#submitBtn").hide(); 
           $.ajax({
               url: "{{ route('verify-otp') }}",
               type: 'POST',
               data: {  otp: $("#otp").val() },
               success: function(response) {
                $("#loader-otp2").hide(); 
                $("#opt-form-confirm").hide(); 
                   $("#submitBtn").show(); // Display a success message
                   $("#opt-form").hide(); 
                   $("#mobile-number").find('p').addClass('text-success').text('Mobile Number Verified')
                   // You can proceed with form submission here
               },
               error: function(xhr) {
                $("#opt-form").find('p').addClass('text-danger').text(xhr.responseJSON.error); 
               }
           });
       })
   });

   function convertTimeTo12HourFormat(inputTime) {
    var timeParts = inputTime.split(":");
    var hours = parseInt(timeParts[0], 10);
    var minutes = parseInt(timeParts[1], 10);
    var ampm = hours >= 12 ? "PM" : "AM";

    hours = hours % 12;
    hours = hours ? hours : 12;

    var formattedTime = hours + ":" + (minutes < 10 ? "0" : "") + minutes + " " + ampm;
    return formattedTime;
}

function convertDateToCustomFormat(inputDate) {
    var dateParts = inputDate.split("-");
    var year = dateParts[0];
    var month = dateParts[1];
    var day = dateParts[2];

    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    var formattedDate = day + "-" + monthNames[parseInt(month, 10) - 1] + "-" + year;
    return formattedDate;
}
   
   </script>
@endsection
