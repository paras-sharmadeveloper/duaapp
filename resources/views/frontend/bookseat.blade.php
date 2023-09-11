@extends('layouts.guest')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="stylesheet">
    <link href="https://fonts.gstatic.com" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,400;1,300&amp;display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
        body {
            background-color: #f2f5f8;
            font-family: 'Karla', sans-serif;
        }

        .main-content .wizard-form .progressbar-list::before {
            content: " ";
            background-color: rgb(155, 155, 155);
            border: 10px solid #fff;
            border-radius: 50%;
            display: block;
            width: 30px;
            height: 30px;
            margin: 9px auto;
            box-shadow: 1px 1px 3px #606060;
            transition: all;
        }

        .main-content .wizard-form .progressbar-list::after {
            content: "";
            background-color: rgb(155, 155, 155);
            padding: 0px 0px;
            position: absolute;
            top: 14px;
            left: -50%;
            width: 100%;
            height: 2px;
            margin: 9px auto;
            z-index: -1;
            transition: all 0.8s;
        }

        .main-content .wizard-form .progressbar-list.active::after {
            background-color: #763cb0;
        }

        .main-content .wizard-form .progressbar-list:first-child::after {
            content: none;
        }

        .main-content .wizard-form .progressbar-list.active::before {
            font-family: "Font Awesome 5 free";
            content: "\f00c";
            font-size: 11px;
            font-weight: 600;
            color: #fff;
            padding: 6px;
            background-color: #763cb0;
            border: 1px solid #763cb0;
            box-shadow: 0 0 0 7.5px rgb(118 60 176 / 11%);
        }

        .progressbar-list {
            color: #6f787d;
        }

        .active {
            color: #000;
        }

        /* card */
        .card img {
            width: 40px;
        }

        .card {
            border: 3px solid rgb(145 145 145);
            cursor: pointer;
        }

        .active-card {
            color: #763cb0;
            font-weight: bold;
            border: 3px solid #763cb0;
        }

        .form-check-input:focus {
            box-shadow: none;
        }

        .bg-color-info {
            background-color: #00d69f;
        }

        .border-color {
            border-color: #ececec;
        }

        .btn {
            padding: 16px 30px;
        }

        .back-to-wizard {
            transform: translate(-50%, -139%) !important;
        }

        .bg-success-color {
            background-color: #87D185;
        }

        .bg-success-color:focus {
            box-shadow: 0 0 0 0.25rem rgb(55 197 20 / 25%);
        }

        .card img {
            margin: auto;
        }

        .row.justify-content-center.form-business.sloting-main .sloting-inner {
            max-height: 500px;
            height: 500px;
            overflow: overlay;
        }
        div#slot-listing h1 {
    width: 100%;
}
    </style>
    <!-- section -->
    <section>
        <!-- container -->
        <div class="container">
            <!-- main content -->
            <div class="main-content">
                <!-- alert box -->
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
                                Please select any card , only then you can move further!
                            </div>
                        </div>
                    </div>
                </div>
                <!-- alert box -->
                <!-- row -->
                <div class="row justify-content-center pt-0 p-4" id="wizardRow">
                    <!-- col -->
                    <div class="col-md-10 text-center">
                        <!-- wizard -->
                        <div class="wizard-form py-4 my-2">
                            <!-- ul -->
                            <ul id="progressBar" class="progressbar px-lg-5 px-0">
                                <li id="progressList-1"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list active">
                                    Step 1</li>
                                <li id="progressList-2"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 2</li>
                                <li id="progressList-3"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Step 3</li>
                                <li id="progressList-4"
                                    class="d-inline-block fw-bold w-25 position-relative text-center float-start progressbar-list">
                                    Done</li>
                            </ul>
                            <!-- /ul -->
                        </div>
                        <!-- /wizard -->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center" id="cardSection">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8">
                        <h3 class="fw-bold pt-5">Select Country</h3>
                        <p class="small pb-5">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom">
                            @foreach ($VenueList as $venue)
                                <div class="col">
                                    <div class="card text-center h-100 py-5 shadow-sm country-section"
                                        data-id="{{ $venue->id }}">
                                        <img src="{{ asset('images/' . $venue->flag_path) }}" alt="Flag Image">

                                        {{-- <i class="fas fa-building card-img-top mx-auto img-light fs-1 pb-1"></i> --}}
                                        <div class="card-body px-0">
                                            <h5 class="card-title title-binding">{{ $venue->country_name }}</h5>
                                            <p class="card-text">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <!-- /cards -->
                    <!-- NEXT BUTTON-->
                    <button type="button"
                        class="btn text-white float-end next mt-4 rounded-3 bg-color-info country-next">Next</button>
                    <!-- /NEXT BUTTON-->
                </div>
                <!-- /col -->

                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center form-business">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8">
                        <h3 class="fw-bold pt-5">Select Venue</h3>
                        <p class="small pb-5">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom" id="venues-listing">

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
                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center form-business sloting-main">
                    <!-- col -->
                    <div class="col-lg-7 col-md-8 slot-in">
                        <h3 class="fw-bold pt-5">Select Slot</h3>
                        <p class="small pb-5">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-5 row-cols-lg-5 g-4 pb-0 border-bottom sloting-inner" id="slot-listing">
 
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
                        <div class="mb-5 pb-5">
                            <!-- Final step -->
                            <div class="alert alert-primary text-center" role="alert">
                                <h5 class="p-4">Finally We are going to submit your information if you want to continue
                                    that
                                    please click on the finish button to finish up your Working process.</h5>
                            </div>

                            <form action="{{ route('booking.submit') }}" method="post" id="booking-form">
                                @csrf
                                <input type="hidden" name="slot_id" id="slot_id_booked" value="">
                                <div class="row g-3 mb-3">
                                    <div class="col">
                                        <input type="text" class="form-control" name="fname" placeholder="Jhon"
                                            aria-label="First name">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="lname" placeholder="Deo"
                                            aria-label="Last name">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col">
                                        <input type="email" class="form-control" name="email"
                                            placeholder="test@example.com" aria-label="Email">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="mobile"
                                            placeholder="+178978978" aria-label="Mobile">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col">
                                        <textarea name="user_question" id="" cols="30" rows="10"
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
                                <!-- /Final step -->
                        </div>
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back rounded-3">Back</button>
                        <button type="submit"
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
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            // hidden things
            $(".form-business").hide();
            $("#successMessage").hide();
            // next button
            $(".next").on({
                click: function() {


                    // select any card
                    var getValue = $(this).parents(".row").find(".card").hasClass("active-card");
                    if (getValue) {
                        $("#progressBar").find(".active").next().addClass("active");
                        $("#alertBox").addClass("d-none");
                        $(this).parents(".row").fadeOut("slow", function() {
                            $(this).next(".row").fadeIn("slow");
                        });

                    } else {
                        $("#alertBox").removeClass("d-none");
                    }
                }
            });
            // back button
            $(".back").on({
                click: function() {
                    $("#progressBar .active").last().removeClass("active");
                    $(this).parents(".row").fadeOut("slow", function() {
                        $(this).prev(".row").fadeIn("slow");
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
                if ($(this).hasClass('country-section')) {
                    getAjax(cardId, 'venue_address')
                } else if ($(this).hasClass('venues-selection')) {
                    getAjax(cardId, 'get_slots')

                } else if ($(this).hasClass('slot-selection')) {
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
                    if (type == 'venue_address') {
                        var html = '';
                        $.each(response, function(key, item) {
                            html += `<div class="col">
                            <div class="card text-center h-100 py-5 shadow-sm venues-selection" data-id="${item.venue_address_id}">
                                <img src="${item.imgUrl}" alt="Flag Image"> 
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${item.address}</h5>
                                    <p class="card-text">Slot Date
                                       <strong> ${item.venue_date} </strong>
                                    </p
                                    <p class="card-text">Slot Timings
                                        ${item.slot_start}
                                        -
                                        ${item.slot_ends}
                                    </p>
                                </div>
                            </div>
                        </div>`;
                        })
                        $("#venues-listing").html(html);
                    }
                    if (type == 'get_slots') {
                        var html = '';
                        if (response.status) {
                            $.each(response.data, function(key, item) {
                                html += `<div class="col">
                                <div class="card text-center h-10 py-0 shadow-sm slot-selection" data-id="${item.id}">
                                    
                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${item.slot_time}</h5>
                                        
                                    </div>
                                </div>
                                </div>`;
                            });
                            $("#slot-listing").html(html);
                            $(".confirm").show();
                            $(".back").show();
                        }else{
                            $("#slot-listing").html("<h1>"+response.message+"</h1>");
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

                // Serialize the form data
                var formData = $(this).serialize();

                // Perform the AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Get the form's action URL
                    type: $(this).attr('method'), // Get the form's HTTP method (POST in this case)
                    data: formData, // Use the serialized form data
                    success: function(response) {
                        // Handle the success response here (e.g., display a message) 
                        $("#wizardRow").fadeOut(300);
                        $("#successForm").fadeOut(300);
                        $("#successMessage").fadeIn(3000);
                        console.log(response); // You can log or display the response as needed
                    },
                    error: function(error) {
                        $("#successMessage").hide();
                        // Handle any errors here (e.g., display an error message)
                        console.error('Error submitting the form.');
                        console.log(error
                            .responseText
                            ); // You can log or display the error response as needed
                    }
                });
            });
        });
    </script>
@endsection
