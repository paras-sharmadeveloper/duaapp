@extends('layouts.guest')
@section('content')

    <style>
       
body{background-color:#f2f5f8;font-family:Karla,sans-serif}.select2-container .select2-selection--single{height:38px}.main-content .wizard-form .progressbar-list::before{content:" ";background-color:#9b9b9b;border:10px solid #fff;border-radius:50%;display:block;width:30px;height:30px;margin:9px auto;box-shadow:1px 1px 3px #606060;transition:none}.main-content .wizard-form .progressbar-list::after{content:"";background-color:#9b9b9b;padding:0;position:absolute;top:14px;left:-50%;width:100%;height:2px;margin:9px auto;z-index:-1;transition:.8s}.main-content .wizard-form .progressbar-list.active::after{background-color:#763cb0}.main-content .wizard-form .progressbar-list:first-child::after{content:none}.main-content .wizard-form .progressbar-list.active::before{font-family:"Font Awesome 5 free";content:"\f00c";font-size:11px;font-weight:600;color:#fff;padding:6px;background-color:#763cb0;border:1px solid #763cb0;box-shadow:0 0 0 7.5px rgb(118 60 176 / 11%)}.progressbar-list{color:#6f787d}.active{color:#000}.card img{width:40px;margin:auto}.card{border:3px solid rgb(145 145 145);cursor:pointer}.active-card{color:#763cb0;font-weight:700;border:3px solid #763cb0}.form-check-input:focus{box-shadow:none}.bg-color-info{background-color:#00d69f}.border-color{border-color:#ececec}.btn{padding:16px 30px}.back-to-wizard{transform:translate(-50%,-139%)!important}.bg-success-color{background-color:#87d185}.bg-success-color:focus{box-shadow:0 0 0 .25rem rgb(55 197 20 / 25%)}.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:500px;height:500px;overflow:overlay}div#slot-listing h1{width:100%}button.btn:hover{color:#000!important;background-color:grey}.card-title{padding:10px 0 4px;font-size:18px;font-weight:500;color:#012970;font-family:Poppins,sans-serif}.danger,.success{text-align:center;font-size:16px}.card-body{padding:0 17px 0 20px}#selfie-image,video#video{height:200px;width:250px}div#captured-image{margin-bottom:15px}.loader{border:5px solid #3498db;border-top:5px solid transparent;border-radius:50%;width:40px;height:40px;animation:1s linear infinite spin}.loader-main{display:flex;justify-content:center;margin-top:5px}.success{color:green;font-weight:900}.danger,.error{color:red}div#error{margin:20px 0}.error.country_code{position:absolute;z-index:999;font-size:9px;bottom:-89px}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}@media (max-width:767px){span.select2.select2-container.select2-container--default{width:100%!important;flex:auto!important}.col{flex-shrink:0!important;flex:auto}.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:290px}.selfie{text-align:center}.p-4{padding:.5rem!important}.card{margin-bottom:20px}.logoo img{height:80px;width:80px}.mt-4{margin-top:.5rem!important}.error.country_code{font-size:14px;bottom:-20px}.row .loader-img{margin:17px!important}}@media (min-width:1024px){.row.justify-content-center.form-business.sloting-main .sloting-inner{max-height:290px}.error.country_code{bottom:-35px}}figcaption{font-size:10px}.thripist-section img{height:100%;width:90%;border-radius:20%}.loader-img{height:64px;width:64px!important}#progressBar .w-25{width:14%!important}.row .loader-img{margin:auto}

    </style>
    <!-- section -->
    <section>
        <!-- container -->
        <div class="container">
            <!-- main content -->
            <div class="main-content">

                <div class="d-flex justify-content-center py-4">
                    <a href="index.html" class="logoo  d-flex align-items-center wuto">
                        <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
                        <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
                    </a>
                </div>


                <div class="row justify-content-center pt-0 p-4" id="wizardRow">
                    
                    <!-- col -->
                    <div class="col-md-10 text-center">

                      
                        <!-- wizard -->
                        <div class="wizard-form py-4 my-2">
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
                                    Done</li>

                                    <img src="/assets/loader.gif" alt="" class="loader-img" style="display: none">
                                  
                            </ul>
                            
                           
                            <!-- /ul -->
                        </div>
                        
                        <!-- /wizard -->
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-7 col-md-8">
                            <!-- svg -->
                            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </symbol>
                            </svg>
                            <!-- /svg -->
                            <div class="alert alert-danger d-flex align-items-center mt-3 d-none mb-0" id="alertBox"
                                role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                                    aria-label="Danger:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    Please select any card , only then you can move furthe  asdr!
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /col -->
                </div>
                <!-- /row -->

                <div class="row justify-content-center" id="cardSection">
                       
                    <div class="col-lg-7 col-md-8">
                        <h3 class="fw-bold">Select Thripist</h3>
                        <p class="small">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom">
                            @foreach ($therapists as $therapist)
                                <div class="col-lg-3 col-md-4">
                                    <div class="card text-center h-60 py-2 shadow-sm thripist-section"
                                        data-id="{{ $therapist->id }}">
                                        <img src="{{ env('AWS_GENERAL_PATH').'images/'.$therapist->profile_pic }}" alt="Profile">

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
                
                    <!-- /cards -->
                    <!-- NEXT BUTTON-->

                    <!-- /NEXT BUTTON-->
                </div>
                <!-- row -->
                <div class="row justify-content-center  form-business" id="cardSection">
                   
                    <div class="col-lg-7 col-md-8">
                        
                        <h3 class="fw-bold">Select Country</h3>
                        <p class="small">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="country-listing">
                            <img src="/assets/loader.gif" alt="" class="loader-img">
                        </div>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info country-next">Next</button>
                    </div>
                    <!-- col -->
                
                    <!-- /cards -->
                    <!-- NEXT BUTTON-->

                    <!-- /NEXT BUTTON-->
                </div>
                <div class="row justify-content-center form-business">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8">
                        <h3 class="fw-bold">Select City</h3>
                        <p class="small">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom" id="city-listing">
                            <img src="/assets/loader.gif" alt="" class="loader-img">
                        </div> 
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /col -->

                <div class="row justify-content-center form-business">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8">
                        <h3 class="fw-bold">Select Date</h3>
                        <p class="small">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom" id="date-listing">
                            <img src="/assets/loader.gif" alt="" class="loader-img">
                        </div>
                        <!-- /cards -->
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
 
                <!-- row -->
                <div class="row justify-content-center form-business sloting-main">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8 slot-in">
                        <h3 class="fw-bold ">Select Slot</h3>
                        <p class="small">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-2 row-cols-lg-5 g-4 pb-0 border-bottom sloting-inner" id="slot-listing">
                            <img src="/assets/loader.gif" alt="" class="loader-img">
                        </div>
                        <!-- /cards -->
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm">Next</button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center py-5 form-business">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8" id="successMessage">
                        <!-- success message -->
                        <div class="position-relative success-content">
                            <img src="https://uploads-ssl.webflow.com/5ef0df6b9272f7410180a013/60c0e28575cd7c21701806fd_q1cunpuhbdreMPFRSFLyfUXNzpqv_I5fz_plwv6gV3sMNXwUSPrq88pC2iJijEV7wERnKXtdTA0eE4HvdnntGo9AHAWn-IcMPKV-rZw1v75vlTEoLF4OdNqsRb7C6r7Mvzrm7fe4.png"
                                class="w-100" id="successImage" alt="success-message">
                            <a href="#" type="button"
                                class="btn bg-success-color py-2 back-to-wizard position-absolute top-100 start-50 translate-middle text-white">Back
                                to Wizad Form</a>
                        </div>
                        <!-- /success message -->
                    </div>
                    <!-- /col -->
                    <!-- col -->
                    <div class="col-lg-7 col-md-8" id="successForm">
                        <div class="mb-5">
                            <!-- Final step -->
                            <div class="alert alert-primary text-center" role="alert">
                                <h5 class="p-4">Finally We are going to submit your information if you want to continue
                                    that
                                    please click on the finish button to finish up your Working process.</h5>
                            </div>

                            <form action="{{ route('booking.submit') }}" method="post" id="booking-form"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="slot_id" id="slot_id_booked" value="">
                                <div class="row g-3 mb-3">
                                    <div class="col col-md-12">
                                        <input type="text" class="form-control" name="fname" placeholder="Jhon"
                                            aria-label="First name">
                                    </div>
                                    <div class="col col-md-12">
                                        <input type="text" class="form-control" name="lname" placeholder="Deo"
                                            aria-label="Last name">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col col-md-12">
                                        <input type="email" class="form-control" name="email"
                                            placeholder="test@example.com" aria-label="Email">
                                    </div> 
                                        <div class="col col-lg-5  col-md-12">

                                            <select id="country_code" name="country_code" class="js-states form-control">
                                                <option value="">select</option>
                                                @foreach ($countryList as $country)
                                                    <option value="{{ $country->phonecode }}"> {{ $country->name }}
                                                        {{ '(+' . $country->phonecode . ')' }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                       
                                        <div class="col col-lg-7 col-md-12" id="mobile-dib">
                                            <input type="number" class="form-control" id="mobile" name="mobile"
                                                placeholder="Enter mobile eg:8884445555" aria-label="Mobile">
                                                <p> </p>
                                        </div>
                                        <div id="opt-form-confirm" style="display: none">
                                            <p></p>
                                            <button type="button" class="" id="sendOtp">Send OTP</button>
                                        </div>
                                        <div id="opt-form" style="display: none">
                                            <input type="text" class="form-control"  name="otp" id="otp" placeholder="Enter OTP">
                                            <p> </p>
                                            <button type="button" class="" id="submit-otp">Submit</button>
                                        </div>
 
                                </div>

                                <div class="row g-3">
                                    <div class="col col-md-12">
                                        <textarea name="user_question" id="" cols="30" rows="3"
                                            placeholder="Put some line of your query" class="form-control"></textarea>

                                    </div>

                                </div>
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_whatsapp"
                                        id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{-- I agree to the terms and conditions --}}
                                        This number is on whatsapp ?
                                    </label>
                                </div>

                                <div class="form-group row mt-3 selfie">
                                    <label for="selfie"
                                        class="col-md-4 col-form-label text-md-right">{{ __('Selfie') }}</label>

                                    <div class="col-md-6">
                                        <button class="btn btn-outline-success" type="button"   id="start-camera">Take Selfie</button>
                                       
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
                                      
                                        <button class="btn btn-outline-primary" type="button" style="display: none;"   id="capture-selfie">Capture</button>
                                    
                                   </div>
                                    <div class="col-lg-6 d-flex justify-content-evenly">
                                       
                                        <button class="btn btn-outline-info mr-2" type="button" style="display: none;"   id="restart-camera">Restart camera</button>
                                        
                                   </div>
                                   
                               </div>
                                <!-- /Final step -->
                        </div> 

                        
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back rounded-3">Back</button>
                        <button type="submit" id="submitBtn"
                            class="btn text-white float-end submit-button rounded-3 bg-color-info">Finish</button>

                       
                         
                        </form>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
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
        $(document).ready(function() {
            // hidden things
           
            $("#successMessage").hide();
            // next button
            $(".next").on({
                click: function() {
                    $(".loader-img").show();

                    // select any card
                    var getValue = $(this).parents(".row").find(".card").hasClass("active-card");
                    if (getValue) {
                        $("#progressBar").find(".active").next().addClass("active");
                        $("#alertBox").addClass("d-none");
                       
                         $(this).parents(".row").fadeOut("slow", function() {
                          $(this).next(".row").fadeIn(); 
                          $(".loader-img").hide();
                         });

                    } else {
                        $("#alertBox").removeClass("d-none").find("div").text(
                            "Please select any card , only then you can move further!");
                    }
                }
            });
            // back button
            $(".back").on({
                click: function() {
                    $(".loader-img").show();
                    $("#progressBar .active").last().removeClass("active");
                   // $(this).next(".row").show();
                    $(this).parents(".row").fadeOut("slow", function() {
                        $(".loader-img").hide();
                        $(this).prev(".row").fadeIn();
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
                    getAjax(cardId, 'get_country')
                }else if ($(this).hasClass('city-selection')) {
                  
                     getAjax(cardId, 'city_section')
                } else if ($(this).hasClass('date-selection')) {
                    getAjax(cardId, 'date_section')
                  //  $("#slot_id_booked").val(cardId);
                }
                else if ($(this).hasClass('date-selection')) {
                    getAjax(cardId, 'date_section')
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
            if(type == 'city_section'){
                
                $(`.country-enable-n`).addClass('d-none'); 
                $(`.country-enable-${id}`).removeClass('d-none');
                return true; 
                $(".loader-img").hide();
            }else if(type == 'date_section'){
                
                $(`.date-enable-n`).addClass('d-none'); 
                $(`.date-enable-${id}`).removeClass('d-none');
                $(".loader-img").hide();
                return true; 
            } 

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
                    $(".loader-img").hide();
                    if (type == 'get_country') {
                        var country = '';
                        $.each(response.country, function(key, item) {
                            country += `<div class="col col-lg-6 col-md-7">
                            <div class="card text-center h-60 py-2 shadow-sm city-selection" data-id="${item.id}">
                                <img src="${item.flag_path}" alt="Flag Image"> 
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${item.name}</h5>
                                   
                                </div>
                            </div>
                        </div>`;
                        })

                        $("#country-listing").html(country);
                        var city = '';
                        var darte = '';
                        $.each(response.venue_address, function(key, item) {
                            city += `<div class="col col-lg-6 col-md-7 country-enable-n country-enable-${item.venue_id} d-none">
                            <div class="card text-center h-60 py-2 shadow-sm date-selection" data-id="${item.venue_address_id}">
                                <img src="${item.imgUrl}" alt="Flag Image"> 
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${item.city}</h5>
                                     
                                </div>
                            </div>
                        </div>`;
                        darte += `<div class="col col-lg-6 col-md-7 date-enable-n date-enable-${item.venue_address_id} d-none">
                            <div class="card text-center h-60 py-2 shadow-sm slot-selection" data-id="${item.venue_address_id}">
                                <img src="${item.imgUrl}" alt="Flag Image"> 
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${item.venue_date}</h5>  
                                </div>
                            </div>
                        </div>`;
                        })
                        $("#city-listing").html(city); 

                        $("#date-listing").html(darte);

                       


                    }
                    // if (type == 'venue_address') {
                    //     var html = '';
                       
                    //     $("#venues-listing").html(html);
                    // }
                    if (type == 'get_slots') {
                        var html = '';
                        if (response.status) {
                            $.each(response.data, function(key, item) {
                                html += `<div class="col col-lg-3 col-md-7">
                                <div class="card text-center h-10 py-0 shadow-sm slot-capture" data-id="${item.id}">
                                    
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${item.slot_time}</h5>
                                        
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
                            $(".back").hide();
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
                event.preventDefault(); // Prevent the default form submission
                $(".loader-img").show();
                // Serialize the form data
                var formData = $(this).serialize();

                // Perform the AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Get the form's action URL
                    type: $(this).attr('method'), // Get the form's HTTP method (POST in this case)
                    data: formData, // Use the serialized form data
                    success: function(response) {
                        $(".loader-img").hide();
                        // Handle the success response here (e.g., display a message) 
                        $("#wizardRow").fadeOut(300);
                        $("#successForm").fadeOut(300);
                        $("#successMessage").fadeIn(3000); 
                    },
                    error: function(error) {
                        $(".loader-img").hide();
                        if(error.responseJSON.status== false){
                            alert(error.responseJSON.message);
                        }
                        if (error.responseJSON && error.responseJSON.errors) {
                            var errors = error.responseJSON.errors;

                            // Clear any existing error messages
                            $('.alert-danger').remove();

                            // Loop through the errors and display them near the respective form fields
                            $.each(errors, function(field, messages) {
                                
                                var inputElement = $('[name="' + field + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after('<div class="error '+field+'">' +
                                messages.join('<br>') + '</div>');
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
                        // Handle the server's re 

                        // You can provide feedback to the user based on the response

                        $("#submitBtn").show();
                        $("#error").removeClass('danger');
                        $("#error").addClass('success').text("Perfect. You can proceed");
                        // Liveness detected, show success message


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
           }else{
               $("#submitBtn").show();
               $("#opt-form-confirm").fadeOut(500);
           }
       });
       $("#sendOtp").click(function(){
           
           $.ajax({
               url: "{{ route('send-otp') }}",
               type: 'POST',
               data: {  
                   country_code : $("#country_code").val(),
                   mobile : $("#mobile").val(),

               },
               success: function(response) {
                $("#opt-form").show();  
                $("#submitBtn").hide(); 
                $("#opt-form").find('p').text(response.message);
                   
               },
               error: function(xhr) {
              
                $("#opt-form-confirm").find('p').text(xhr.responseJSON.message);
                    
               }
           });
       })

       $("#submit-otp").click(function(){
           $("#opt-form").show(); 
           
           $("#submitBtn").hide(); 
           $.ajax({
               url: "{{ route('verify-otp') }}",
               type: 'POST',
               data: {  otp: $("#otp").val() },
               success: function(response) {
                $("#opt-form-confirm").hide(); 
                   $("#submitBtn").show(); // Display a success message
                   $("#opt-form").hide(); 
                   $("#mobile-dib").find('p').addClass('text-success').text('Mobile Number Verified')
                   // You can proceed with form submission here
               },
               error: function(xhr) {
                $("#opt-form").find('p').text(xhr.responseJSON.error); 
               }
           });
       })
   });

   
   </script>
@endsection
