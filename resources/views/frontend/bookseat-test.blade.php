@extends('layouts.guest')
@section('content')
    <style>
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        li {
            color: #ffffff;
        }

        body {
            background-color: rgb(29, 29, 29);
            font-family: Karla, sans-serif,
        }

        ul#progress-bar li {
            font-size: 17px;
        }
        .select2-container .select2-selection--single {
            height: 38px
        }

        .main-content .wizard-form .progressbar-list::before {
            content: " ";
            background-color: #9b9b9b;
            border: 10px solid #fff;
            border-radius: 50%;
            display: block;
            width: 30px;
            height: 30px;
            margin: 9px auto;
            box-shadow: 1px 1px 3px #606060;
            transition: none
        }

        .main-content .wizard-form .progressbar-list::after {
            content: "";
            background-color: #9b9b9b;
            padding: 0;
            position: absolute;
            top: 14px;
            left: -50%;
            width: 100%;
            height: 2px;
            margin: 9px auto;
            z-index: -1;
            transition: .8s
        }

        .main-content .wizard-form .progressbar-list.active::after {
            background-color: #763cb0
        }
        .statement-notes {
            height: 300px;
            overflow: auto;
        }

        .main-content .wizard-form .progressbar-list:first-child::after {
            content: none
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
            box-shadow: 0 0 0 7.5px rgb(118 60 176 / 11%)
        }

        .progressbar-list {
            color: #6f787d
        }

        .active {
            color: #000
        }

        .card img {
            width: 40px;
            margin: auto
        }

        .card {
            border: 3px solid rgb(145 145 145);
            cursor: pointer
        }

        .active-card {
            color: #763cb0;
            font-weight: 700;
            border: 6px solid #15d92b
        }

        .form-check-input:focus {
            box-shadow: none
        }

        .bg-color-info {
            background-color: #00d69f
        }

        .border-color {
            border-color: #ececec
        }

        .btn {
            padding: 16px 30px
        }

        .back-to-wizard {
            transform: translate(-50%, -139%) !important
        }

        .bg-success-color {
            background-color: #87d185
        }

        .bg-success-color:focus {
            box-shadow: 0 0 0 .25rem rgb(55 197 20 / 25%)
        }

        .row.justify-content-center.form-business.sloting-main .sloting-inner {
            max-height: 500px;
            height: 500px;
            overflow: overlay
        }

        div#slot-listing h1 {
            width: 100%
        }

        button.btn:hover {
            color: #fff !important;
            background-color: grey
        }
        .btn.back{
            color: #000 !important;
            background-color: grey;
            font-size: 18px;
        }
        .btn.back:hover{
            color: #fff !important;
        }

        .card-title {
            padding: 10px 0 8px;
            font-size: 24px;
            font-weight: 500;
            color: #012970;
            font-family: Poppins, sans-serif
        }

        .danger,
        .success {
            text-align: center;
            font-size: 16px
        }

        .card-body {
            padding: 0 17px 0 20px
        }

        #selfie-image,
        video#video {
            height: 250px;
            width: 300px
        }

        div#captured-image {
            margin-bottom: 15px
        }



        .success {
            color: green;
            font-weight: 900
        }

        .danger,
        .error {
            color: red;
            font-weight: bold
        }

        div#error {
            margin: 10px 0
        }

        @keyframes spin {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        @media (max-width:767px) {
            #booknowStart {
                margin-top: 50% !important;
            }

            div#type-listing,
            #country-listing,
            #city-listing,
            #date-listing {
                max-height: 340px;
                overflow-x: scroll;
                height: 210px;
            }

            .row.justify-content-center.form-business.sloting-main .sloting-inner {
                height: 200px !important;
                max-height: 330px !important;
                overflow: overlay;
            }


            div#mobile-number label {
                font-size: 12px !important;
            }

            #startBooking {
                width: 100% !important;
            }

            .container-fluid {
                padding: 0px !important;
            }

            .head label {
                font-size: 15px !important;
                color: #fff !important;
            }

            div#loader {
                text-align: center !important;
            }

            .head {
                display: inherit !important;
                margin-top: 10px;
            }

            span.select2.select2-container.select2-container--default {
                width: 100% !important;
                flex: auto !important
            }

            .col {
                flex-shrink: 0 !important;
                flex: auto
            }

            .row.justify-content-center.form-business.sloting-main .sloting-inner {
                max-height: 375px
            }

            .selfie {
                text-align: center
            }

            .p-4 {
                padding: .5rem !important
            }

            .card {
                margin-bottom: 20px
            }

            .logoo img {
                height: 50px;
                width: 50px
            }

            .mt-4 {
                margin-top: .5rem !important
            }

            .error.country_code {
                font-size: 14px;
                bottom: -20px
            }

            .row .loader-img {
                margin: 17px !important
            }

            .thripist-section img {
                height: 100% !important;
                width: 100% !important;
            }

            .col-lg-6 {
                flex: 0 0 auto;
                width: 50% !important;
            }

            .otp-btn {
                text-align: center;
                margin: 12px 0px;
            }

            div#opt-form-confirm {
                text-align: center;
            }

            .cusmhtn {
                font-size: 12px;
            }

            /* .card.text-center.h-60.shadow-sm.thripist-section img {
                        max-height: 180px !important;
                    } */
            .col-xs-6.col-sm-4.col-md-4.col-lg-3 {
                width: 50% !important;
            }

            button#sendOtp {
                margin-top: 30px;
                float: none !important;
            }

        }

        .btn.next,  #startBooking,.language-selection  {
            background: #f9d20a !important;
            color: #000 !important;
            font-size: 18px;

        }
        .language-selection  {
            width: 90%
        }

        @media (min-width:1024px) {
            .row.justify-content-center.form-business.sloting-main .sloting-inner {
                max-height: 380px
            }

            .error.country_code {
                bottom: -35px
            }

            /* .thripist-section img {

                    max-height: 264px !important;
                } */


        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .head label {
                font-size: 20px !important;
            }
        }


        figcaption {
            font-size: 10px
        }

        .thripist-section img {
            height: 100%;
            width: 100%;
            /* max-height: 300px;  */
        }

        .loader-img {
            height: 64px;
            width: 64px !important
        }

        #progressBar .w-25 {
            width: 14% !important
        }

        .row .loader-img {
            margin: auto
        }

        /* .col-lg-6 {
                        flex: 0 0 auto;
                        width: 20%;
                    } */
        .select2-container {
            width: 100%;
        }

        .select2-container {
            width: 100% !important;
        }

        /* css loader start  */


        .row .no-data {
            width: 100% !important;
            text-align: center;
            font-size: 26px;
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

        .wrapper ul li.d-none {
            display: none;
        }

        .wrapper ul li:last-child:after {
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
            background: #000000;
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
            background: #FFEB3B;
        }

        .wrapper ul li.active~li:before {
            background: #dde2e5;
        }

        .wrapper ul li.active~li:after {
            background: rgba(221, 226, 229, 0.4);
        }

        .wrapper ul li.active:before {
            background-color: #0c0c0c;
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

        #sendOtp label {
            color: #fff;
        }

        label.form-check-label {
            color: #fff;
        }

        .checkSlot img {
            position: absolute;
            top: 5px;
            right: 4px;
            height: 28px !important;
        }

        label {
            color: white;
        }

        /* button#sendOtp {
            margin-top: 30px;
            float: right;
        } */
        div#slot-information-user {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }

        #slot-information-user select.change-timezone.form-control,
        #slot-information-user .select2-container {
            width: 30% !important;
            z-index: 99999999;
        }

        #slot-information-user .select2-container--default .select2-selection--single .select2-selection__rendered {

            line-height: 50px !important;
        }

        #slot-information-user .select2-container--default .select2-selection--single .select2-selection__arrow {

            top: 12px !important;
        }

        #slot-information-user .select2-container .select2-selection--single {

            height: 50px !important;
        }

        /* .box {
         border: 1px solid #CCC;
         padding: 40px 25px;
         background: #FFF;
         max-width: 400px;
         position: relative;
         border-radius: 3px;
            margin: 30px auto;
        } */
        .box.ofh {
            overflow: hidden;
        }

        /* Ribbon 1 */
        .top-cross-ribbon {
            background: #090909;
            padding: 7px 50px;
            color: #FFF;
            position: absolute;
            top: 0;
            right: -50px;
            transform: rotate(45deg);
            border: 1px dashed #FFF;
            box-shadow: 0 0 0 3px #090909;
            margin: 5px;
        }

        /* Ribbon 2*/
        .arrow-ribbon {
            background: #090909;
            color: #FFF;
            padding: 4px 4px;
            position: absolute;
            top: 0px;
            right: -1px;
        }

        .arrow-ribbon:before {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            content: "";
            left: -12px;
            border-top: 15px solid transparent;
            border-right: 12px solid #090909;
            border-bottom: 15px solid transparent;
            width: 0;
        }

        /* Ribbon 3 */
        .bottom-ribbon {
            background: #090909;
            color: #FFF;
            padding: 7px 50px;
            position: absolute;
            bottom: 10px;
            right: -1px;
            border-radius: 20px 0 0 20px;
        }

        .bottom-ribbon:after {
            position: absolute;
            right: -25px;
            top: -18px;
            bottom: 0;
            z-index: 9999;
            content: "";
            border-bottom: 43px solid #090909;
            border-left: 38px solid transparent;
            border-right: 20px solid transparent;
            width: 42px;
            z-index: -1;
        }

        /*Ribbon 4 */
        .half-circle-ribbon {
            background: #090909;
            color: #FFF;
            height: 60px;
            width: 60px;
            text-align: right;
            padding-top: 10px;
            padding-right: 10px;
            position: absolute;
            top: -1px;
            right: -1px;
            flex-direction: row;
            border-radius: 0 0 0 100%;
            border: 1px dashed #FFF;
            box-shadow: 0 0 0 3px #EA4335;
        }

        /* Ribbon 5 */
        .cross-shadow-ribbon {
            position: absolute;
            background: #090909;
            top: -15px;
            padding: 10px;
            margin-left: 15px;
            color: #FFF;
            border-radius: 0 0 2px 2px;
        }

        .cross-shadow-ribbon:before {
            content: "";
            position: absolute;
            left: -15px;
            right: 0;
            top: 0;
            bottom: 0;
            width: 0;
            height: 0;
            border-bottom: 15px solid #090909;
            border-left: 15px solid transparent;
        }

        /* Ribbon 6 */
        .cover-ribbon {
            height: 115px;
            width: 115px;
            position: absolute;
            right: -8px;
            top: -8px;
            overflow: hidden;
        }

        .cover-ribbon .cover-ribbon-inside {
            background: #090909;
            color: #FFF;
            transform: rotate(45deg);
            position: absolute;
            right: -35px;
            top: 15px;
            padding: 10px;
            min-width: 127px;
            text-align: center;
        }

        .cover-ribbon .cover-ribbon-inside:before {
            width: 0;
            height: 0;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 10px solid #090909;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            content: "";
            top: 35px;
            transform: rotate(-45deg);
        }

        .cover-ribbon .cover-ribbon-inside:after {
            width: 0;
            height: 0;
            border-top: 7px solid transparent;
            border-left: 10px solid #090909;
            border-bottom: 7px solid transparent;
            position: absolute;
            left: 95%;
            right: 0;
            top: 34px;
            bottom: 0;
            content: "";
            transform: rotate(-45deg);
        }

        div#errors {
            text-align: center;
            color: red;
            font-size: 20px;
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
                    <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                        {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                        <img src="https://kahayfaqeer.org/assets/kahe-faqeer-white-1.png" alt="">

                        <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
                    </a>
                </div>


                <div class="row justify-content-center pt-0 p-4" id="wizardRow" style="display: none">

                    <!-- col -->
                    <div class="col-md-12 text-center wizard-form">
                        <div class="wrapper">
                            <ul class="status-line" id="progress-bar">
                                <li class="active">{{ trans('messages.nav-dua-option') }}</li>
                                <li>{{ trans('messages.nav-city-option') }}</li>
                                <li>{{ trans('messages.nav-final-option') }}</li>
                            </ul>
                        </div>


                    </div>
                </div>

                @if(empty($locale))

                    <div class="row justify-content-center"  id="Lang-Section">
                        <h3 class="text-center">{{ trans('messages.select-lang-head') }}</h3>

                        <div class="col-lg-12 col-md-12">
                            @foreach (['en'=> 'English', 'ur'=> 'اردو'] as $key => $lang)

                            <div class="row row-cols-3 d-flex justify-content-center">
                                <button type="button" class="btn text-white float-end mt-4 rounded-3 bg-color-info language-selection"
                                    data-lang="{{ route('book.show.test',[$key]) }}">
                                    </span>
                                     <b> {{ $lang  }}   </b>

                                </button>
                                {{-- <button class="btn text-white float-end next mt-4 rounded-3 bg-color-info " id="startBooking"> Start Booking </button> --}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else

                    <div class="row justify-content-center" id="booknowStart">

                    <div class="col-lg-12 col-md-12">

                        <div class="row row-cols-3 d-flex justify-content-center">
                            <button type="button" class="btn text-white float-end mt-4 rounded-3 bg-color-info"
                                id="startBooking" data-loading="Loading..." data-success="Done" data-default="Next">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                    style="display:none">
                                </span>
                                <b> {{ trans('messages.startbooking') }} </b>
                            </button>

                         </div>
                         <div class="myanoucements text-center py-2">
                            <p>{{ ($reasons) ? $reasons->reason_english : ''}}</p>
                            <p> {{($reasons) ? $reasons->reason_urdu : ''}}</p>
                         </div>

                    </div>


                </div>


                @endif





                <div class="row justify-content-center form-business" id="cardSection" style="display: none">

                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">{{ trans('messages.select-option') }}</h3>

                            <label></label>
                        </div>

                        <p class="error d-none text-center alertBox">{{ trans('messages.select-option-card') }}</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="thripis1t-main">

                            @foreach (['dua' => 'Dua' , 'dum' => 'Dum'] as $key => $dua)
                                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 col">
                                    <div class="card text-center h-60  shadow-sm dua-section"
                                        data-id="{{ $key }}">
                                        <div class="card-body px-0">
                                            <h5 class="card-title title-binding">{{ trans('messages.'. $key ) }}</h5>
                                            <p class="card-text">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="{{ trans('messages.loading-btn')}}..." data-success="{{ trans('messages.done-btn') }}" data-default="{{ trans('messages.next-btn') }}">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b>{{ trans('messages.next-btn') }}</b>
                        </button>
                        {{-- <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="Loading..." data-success="Done" data-default="Next">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> Next</b>
                        </button> --}}
                    </div>
                </div>

                {{-- <div class="row justify-content-center  form-business" style="display: none">

                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Type</h3>
                            <label></label>
                        </div>
                        <p class="error d-none text-center alertBox">Please select at least one card</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom main-inner" id="type-listing">

                        </div>
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="Loading..." data-success="Done" data-default="Next">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> Next</b>
                        </button>
                    </div>
                </div> --}}

                <!-- row -->
                {{-- <div class="row justify-content-center  form-business" style="display: none">

                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Country</h3>
                            <label></label>
                        </div>

                        <p class="error d-none text-center alertBox">Please select at least one card</p>

                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom main-inner" id="country-listing">

                        </div>
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>

                        <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="Loading..." data-success="Done" data-default="Next">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> Next</b>
                        </button>
                    </div>
                </div> --}}
                <div class="row justify-content-center form-business" style="display: none">
                    <!-- col -->
                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">{{ trans('messages.select-city') }}</h3>
                            <label></label>
                        </div>
                        <p class="error d-none text-center alertBox">{{ trans('messages.select-option') }}</p>
                        <!-- cards -->
                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom main-inner" id="city-listing">

                        </div>
                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">{{ trans('messages.back-btn') }}</button>
                        <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="{{ trans('messages.loading-btn')}}..." data-success="{{ trans('messages.done-btn') }}" data-default="{{ trans('messages.next-btn') }}">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b>{{ trans('messages.next-btn') }}</b>
                        </button>
                        <!-- /NEXT BUTTON-->
                    </div>
                    <!-- /col -->
                </div>
                <!-- /col -->

                {{-- <div class="row justify-content-center form-business" style="display: none">

                    <div class="col-lg-12 col-md-12">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Event Date</h3>
                            <label></label>
                        </div>
                        <p class="error d-none text-center alertBox">Please select at least one card</p>

                        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom main-inner" id="date-listing">

                        </div>
                        <button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>
                        <button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="Loading..." data-success="Done" data-default="Next">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> Next</b>
                        </button>
                    </div>
                </div> --}}

                <!-- row -->
                {{-- <div class="row justify-content-center form-business sloting-main" style="display: none">

                    <div class="col-lg-12 col-md-12 slot-in">
                        <div class="head mb-4">
                            <h3 class="fw-bold text-center">Select Event Slot</h3>
                            <label></label>
                        </div>
                        <p class="error d-none text-center alertBox">Please select at least one card</p>

                        <div id="slot-information-user">
                            <label> Your Current Timezone:</label>
                            <select class="change-timezone form-control" name="timezone" class="js-states form-control"
                                id="timezone">
                                @foreach ($timezones as $country)
                                    @foreach ($country->timezones as $timezone)
                                        <option value="{{ $timezone->timezone }}"> {{ $timezone->timezone }}
                                            ({{ $country->nicename }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="row row-cols-2 row-cols-lg-5 g-4 pb-0 border-bottom sloting-inner main-inner"
                            id="slot-listing">

                        </div>
                        <button type="button"
                            class="btn btn-info text-white float-start back mt-4 rounded-3">Back</button>

                        <button type="button" id="slot-next"
                            class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm"
                            data-loading="Loading..." data-success="Done" data-default="Next">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> Next</b>
                        </button>

                    </div>
                </div> --}}
                <!-- /row -->
                <!-- row -->
                <div class="row justify-content-center py-5 form-business" style="display: none">

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
                        <div class="mb-1">
                            <!-- Final step -->
                            <div class="alert alert-danger text-center d-nne" role="alert" id="myalert">
                            </div>

                            <form action="{{ route('booking.submit') }}" method="post" id="booking-form"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="slot_id" id="slot_id_booked" value="">
                                <input type="hidden" name="dua_type" id="dua_type" value="">
                                <input type="hidden" name="lang" id="lang" value="{{ $locale }}">
                                <input type="hidden" name="captured_user_image" id="image-input" value="">
                                <img src="" id="showhere" width="200px" height="200px">


                                {{-- <div class="row g-3 mb-3">

                                    <div class="col col-md-6">
                                        <label class="mb-2"> First Name </label>
                                        <input type="text" class="form-control" name="fname"
                                            placeholder="Enter your first name" aria-label="First name">
                                    </div>
                                    <div class="col col-md-6">
                                        <label class="mb-2"> Last Name </label>
                                        <input type="text" class="form-control" name="lname"
                                            placeholder="Enter your last name" aria-label="Last name">
                                    </div>

                                </div> --}}
                                {{-- <div class="row g-3 mb-3" id="email-contaniner">
                                    <div class="col col-md-12">
                                        <label class="mb-2"> Email</label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="Eg:test@example.com" aria-label="Email" id="email">
                                        <p> </p>
                                    </div>
                                </div> --}}


                                <div class="row g-3 mb-0">
                                    <div class="col col-lg-6  col-md-6" id="countryCodeDiv">
                                        <label class="mb-2"> {{ trans('messages.country-label') }}</label>
                                        <select id="country_code" name="country_code" class="js-states form-control">
                                            <option value="">select</option>
                                            @foreach ($countryList as $country)
                                                <option value="{{ $country->phonecode }}"> {{ $country->name }}
                                                    {{ '(+' . $country->phonecode . ')' }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="col col-lg-6 col-md-6" id="mobile-number">
                                        <label class="mb-2"> {{ trans('messages.mobile-label') }}</label>
                                        <input type="text" min="0" inputmode="numeric" pattern="[0-9]*"
                                        class="form-control" id="mobile" name="mobile"
                                            placeholder="Eg:8884445555" aria-label="Mobile">
                                        <p> </p>
                                    </div>
                                    <div id="otpVerifiedMessage" class="text-center">
                                        <p></p>
                                    </div>

                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col col-lg-12 col-md-12 text-center" id="opt-form-confirm"
                                        style="display: none">
                                        <label></label>
                                        <button type="button" id="sendOtp" class="btn-cst m btn btn-primary testbtn"
                                            type="button" data-loading="Sending OTP" data-success="Success"
                                            data-default="Send OTP">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true" style="display:none">
                                            </span>
                                            <label> Sent OTP</label>
                                        </button>
                                        <p></p>

                                    </div>
                                </div>
                                <div id="opt-form" style="display: none">
                                    <div class="row mt-2">
                                        <div class="col col-lg-5 col-md-12  col-sm-12">
                                            <input type="text" class="form-control" name="otp" id="otp"
                                                placeholder="Enter OTP">
                                            <input type="hidden" name="otp-verified" value="" id="otp-verified">
                                            <p></p>
                                        </div>
                                        <div class="col col-lg-7 col-md-12  col-sm-12 otp-btn">
                                            <button type="button" id="submit-otp"
                                                class="btn-cst  btn btn-primary testbtn" type="button"
                                                data-loading="Verifying OTP" data-success="Success"
                                                data-default="Submit">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" style="display:none">
                                                </span>
                                                <label> Submit</label>
                                            </button>

                                        </div>

                                    </div>
                                </div>
                                <video id="camera-preview" autoplay style="display: none"></video>

                                {{-- <div class="row g-3">
                                    <div class="col col-md-12">
                                        <textarea name="user_question" id="" cols="30" rows="3"
                                            placeholder="Put some line of your query" class="form-control"></textarea>

                                    </div>

                                </div> --}}
                                {{-- <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_whatsapp"
                                        id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        This number is on whatsapp ?
                                    </label>
                                </div> --}}

                                {{-- <div class="form-group row mt-3 selfie" id="make-selfie-area">

                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-outline-success btn-cst" type="button"
                                            id="start-camera">Take Selfie</button>
                                        <div id="camera-view" style="display: none;">
                                            <video id="video" autoplay playsinline></video>
                                        </div>
                                        <div id="captured-image" style="display: none;">

                                            <img id="selfie-image" src="" alt="Captured Selfie">
                                            <div id="error"></div>
                                        </div>


                                        <input type="hidden" id="selfie" name="selfie" required>

                                    </div>

                                </div> --}}
                                {{-- <div class="row">
                                    <div class="loader-main" id="loader-main" style="display: none">
                                        <div class="loader mb-3"></div>

                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-evenly">

                                        <button class="btn btn-outline-primary btn-cst" type="button"
                                            style="display: none;" id="capture-selfie">Capture</button>

                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-evenly">

                                        <button class="btn btn-outline-info mr-2 btn-cst" type="button"
                                            style="display: none;" id="restart-camera">Restart camera</button>

                                    </div>
                                    <div id="errors"></div>

                                </div>  --}}

                                <input type="hidden" name="timezone" id="timezone-hidden">

                                <div class="disclaimer">
                                    <p style="font-size:10px">
                                        {{ trans('messages.disclaimer') }}

                                    </p>
                                </div>


                        </div>

                        {{-- <input type="hidden" name="selfie_required" id="selfie_required" value="yes"> --}}




                        <!-- NEXT BUTTON-->
                        <button type="button" class="btn btn-dark text-white float-start back rounded-3">{{ trans('messages.back-btn') }}</button>

                        <button type="submit" id="submitBtn"
                            class="btn text-white float-end submit-button rounded-3 bg-color-info" type="submit"
                            data-loading="{{ trans('messages.submiting-btn')}}..." data-success="{{ trans('messages.done-btn') }}" data-default="Finish">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b> {{ trans('messages.submit-btn') }}</b>
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
    <script>
            var translations = {!! json_encode(trans('messages')) !!};

            var lang = "{{$locale}}";

        $(".form-business").hide();
        var imagePath = "{{ env('AWS_GENERAL_PATH') . 'images/' }}";
        var NoImage = "{{ asset('assets/theme/img/avatar.png') }}";

        $(".language-selection").click(function(){
            var link = $(this).attr('data-lang');

            location.href= link
        })


        $("#startBooking").click(function() {
            var html = '';

            var loadingText = $(this).attr('data-loading');
            var successText = $(this).attr('data-success');
            var defaultText = $(this).attr('data-default');

            $(this).find('span').show()
            $(this).find('b').text(loadingText)
            $.ajax({
                url: "{{ route('booking.get.users') }}", // Get the form's action URL
                type: 'Post', // Get the form's HTTP method (POST in this case)
                // data: formData, // Use the serialized form data
                success: function(response) {
                    $("#slot-information-user").find('label').text("Your Current Timezone:" + response
                        .currentTimezone);
                    $("#timezone-hidden").val(response.currentTimezone)
                    if (response.status) {
                        $(this).find('span').hide()
                        $(this).find('b').text(defaultText)
                        var phoneCode = response.phoneCode;
                        console.log("country_code",phoneCode)
                        $("#country_code").attr("data-ud",phoneCode)
                        $("#country_code").val(phoneCode).trigger('change');
                        $.each(response.data, function(key, item) {
                            var img = '';

                            if (item.profile_pic) {
                                var fullImg = imagePath + item.profile_pic;
                                img = `<img src="${fullImg}">`;
                            } else {
                                img = '<img src="/assets/theme/img/avatar.png">';
                            }
                            html += `<div class="col-xs-6 col-sm-4 col-md-4 col-lg-3 col">
                                    <div class="card text-center h-60  shadow-sm  city-selection" data-id="${item.id}">

                                        <div class="card-body px-0 p-2">
                                            <h5 class="card-title title-binding"><strong>${item.name}</strong></h5>
                                            <p class="card-text">
                                        </p></div>
                                    </div></div>`;

                        });


                    } else {
                        html =
                            `<div class="col-lg-12 text-center"><p class="no-data"> No Venue Created yet </p> </div>`;
                    }
                    $("#booknowStart").fadeOut();
                    $("#cardSection").fadeIn(500)
                    $("#wizardRow").fadeIn(500)

                    $("#thripist-main").html(html)



                },
                error: function(error, xhr) {


                }
            });



        })

        $("#timezone").select2({
            placeholder: "Your Preferred Timezone",
            allowClear: true
        });
        $("#country_code").select2({
            placeholder: "Select country",
            allowClear: true
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(document).on("click", ".checkSlot", function() {
            $this = $(this);
            $("#slot-next").hide();
            var id = $this.attr('data-id');
            $(".load-img").hide();
            $this.find(".load-img").show();

            $.ajax({
                url: "{{ route('check-available') }}", // Update the URL to your Laravel endpoint
                method: 'POST',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $("#slot-next").show();
                        $this.find(".load-img").hide();
                    }

                },
                error: function(error) {

                    $(this).addClass('invalid-slot').fadeOut(2000);
                    $("#slot-next").hide();
                }
            });

        });
        $(document).ready(function() {

            $(".next").on({
                click: function() {
                    $this = $(this);
                    var getValue = $(this).parents(".row").find(".card").hasClass("active-card");
                    $activeCard = $(this).parents(".row").find(".active-card");
                    if (getValue) {


                        var oldTitle = $("#remeber-steps-app").val();

                        var title = $activeCard.find(".title-binding").text();
                        var cardId = $activeCard.attr("data-id");
                        var CityName = $activeCard.attr("data-city");
                        var event = $activeCard;

                        if (event.hasClass('dua-section')) {
                            $("#dua_type").val($activeCard.attr("data-id"));
                            getAjax(cardId, 'get_city', $this)
                            // getAjax(cardId, 'get_country', $this)
                        }
                        // else if (event.hasClass('type-selection')) {
                        //     getAjax(cardId, 'get_country', $this)
                        // }

                        // if (event.hasClass('thripist-section')) { )
                        //     getAjax(cardId, 'get_country', $this)
                        // } else if (event.hasClass('type-selection')) {
                        //     getAjax(cardId, 'get_country', $this)
                        // }
                        // if (event.hasClass('city-selection')) {
                        //     $("#dua_type").val(getValue);

                        //     getAjax(cardId, 'get_city', $this)
                        // }
                        else if (event.hasClass('date-selection')) {
                           var duaType = $("#dua_type").val();
                            getAjax(cardId, 'get_slot_book', $this, CityName , duaType)
                            //  $("#slot_id_booked").val(cardId);
                            // getAjax(cardId, 'get_date', $this, CityName)
                            //  $("#slot_id_booked").val(cardId);
                        }
                        // else if (event.hasClass('slot-selection')) {
                        //     $("#slot-information-user").attr('data-id', cardId);
                        //     getAjax(cardId, 'get_slots', $this)
                        //     // $("#slot_id_booked").val(cardId);
                        // } else if (event.hasClass('slot-capture')) {
                        //     $("#slot_id_booked").val(cardId);
                        //     $this.parents(".row").fadeOut("slow", function() {
                        //         $(this).next(".row").fadeIn();
                        //         $(this).next(".row").find('.head>label').text(oldTitle)

                        //     });
                        // }


                        if (oldTitle == '') {
                            oldTitle = title;
                        } else {
                            oldTitle += " > " + title;
                        }

                        $(this).parents(".row").find(".head>label").text(oldTitle);
                        $("#remeber-steps-app").val(oldTitle);

                        $(this).parents('.justify-content-center').find('.head>label').text(oldTitle);
                        $("#progress-bar").find(".active").next().addClass("active").prev().removeClass(
                            'active');

                        $(this).parents(".row").find(".alertBox").addClass("d-none")


                    } else {


                        $("#loader").hide();
                        $(this).parents(".row").find(".alertBox").removeClass("d-none").text(
                            "Please select any card , only then you can move further!")

                        // $("alertBox").removeClass("d-none").find("div").text("Please select any card , only then you can move further!");
                    }
                }
            });
            // back button
            $(".back").on({
                click: function() {
                    $(".next").show();
                    $("#progress-bar").find(".active").removeClass('active').prev().addClass('active')

                    var currentTitle = $('#remeber-steps-app').val();
                    currentTitle = currentTitle.split(' > ');
                    currentTitle.pop();

                    var newString = currentTitle.join(' > ');
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
                $(this).parent(".col").siblings().children(".card").removeClass("active-card");
            })
            //back to wizard
            $(".back-to-wizard").on({
                click: function() {
                    location.reload(true);
                }
            });
        });


        function getAjax(id, type, nextBtn, optional = '' , duaType ='') {

            var loadingText = nextBtn.attr('data-loading');
            var successText = nextBtn.attr('data-success');
            var defaultText = nextBtn.attr('data-default');

            nextBtn.find('span').show()
            nextBtn.find('b').text(loadingText)

            $.ajax({
                type: 'POST',
                url: "{{ route('booking.ajax') }}",
                data: {
                    "id": id,
                    "type": type,
                    'optional': optional,
                    "timezone": $("#timezone-hidden").val(),
                    'duaType' : duaType
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {


                    // if (type == 'get_type') {

                    //     var typed = '';
                    //     if (response.status) {

                    //     $.each(response.data.type, function(key, item) {

                    //         var meetingType = 'Online';
                    //         if (item.name == 'on-site') {
                    //             meetingType = 'Physical';
                    //         }
                    //         typed += `<div class="col col-lg-3 col-md-7 box">
                //             <div class="card text-center h-60 py-2 shadow-sm type-selection"
                //              data-id="${item.venue_address_id}"
                //              data-type="${item.name}">
                //                 <img src="${item.flag_path}" alt="Flag Image">
                //                 <div class="card-body px-0">
                //                     <div class="arrow-ribbon">${item.day_left}</div>
                //                     <h5 class="card-title title-binding">${meetingType}</h5>
                //                 </div>
                //             </div>
                //         </div>`;
                    //     })
                    // }else{
                    //     typed = '<p class="no-data"> No Data Found </p>';
                    // }
                    //     $("#type-listing").html(typed);
                    //     nextBtn.find('b').text(defaultText)

                    // }


                    if (type == 'get_country') {
                        var country = '';
                        if (response.status) {

                            $.each(response.data.country, function(key, item) {
                                var meetingType = 'Online';
                                if (item.type == 'on-site') {
                                    meetingType = item.name;
                                }
                                country += `<div class="col col-lg-3 col-md-7">
                            <div class="card text-center h-60 py-2 shadow-sm city-selection" data-id="${item.venue_id}">
                                <img src="${item.flag_path}" alt="Flag Image">
                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${meetingType}</h5>

                                </div>
                            </div>
                        </div>`;
                            })
                        } else {
                            country = '<p class="no-data"> No Data Found </p>';
                        }
                        $("#country-listing").html(country);
                        nextBtn.find('b').text(defaultText)

                    }
                    if (type == 'get_city') {

                        var city = '';
                        if (response.status) {
                            var i = 1;
                            var cityArray = Object.values(response.data.city);
                            cityArray.sort(function(a, b) {
                                return a.seq - b.seq;
                            });


                            $.each(cityArray, function(key, item) {

                                var meetingType = 'Online';
                                if (item.type == 'on-site') {
                                    meetingType = translations[item.name] || item.name;
                                }


                                // <img src="${item.flag_path}" alt="Flag Image">
                                city += `<div data-sq="${item.seq}" class="col col-lg-3 col-md-7 country-enable-n country-enable-${item.id}">
                                        <div class="card text-center h-60 py-2 shadow-sm date-selection" data-id="${item.id}" data-city="${item.name}">

                                            <div class="card-body px-0">
                                                <h5 class="card-title title-binding">${meetingType}</h5>

                                            </div>
                                        </div>
                                    </div>`;
                            })


                        } else {
                            city = `<p class="no-data"> ${translations['no_dum_dua']}</p>`;
                        }

                        $("#city-listing").html(city);


                        nextBtn.find('b').text(defaultText)


                    }



                    if(type == 'get_slot_book'){
                        var dAte='';

                        if (response.token_id) {
                            $("#booking-form").show();
                            $("#submitBtn").show();


                            $("#slot_id_booked").val(response.slot_id);
                            if(lang == 'en'){
                                $("#successForm").find(".alert").text(response.message).addClass('d-none')
                            }else{
                                $("#successForm").find(".alert").text(response.message_ur).addClass('d-none')
                            }

                        }else if (response.status == false) {
                            dAte = '<p class="no-data">'+response.message+'</p>';

                            $("#booking-form").hide();
                            $("#submitBtn").hide();
                            if(lang == 'en'){
                                $("#successForm").find(".alert").text(response.message).removeClass('d-none')
                            }else{
                                $("#successForm").find(".alert").text(response.message_ur).removeClass('d-none')
                            }
                            // $("#successForm").find(".alert").text(response.message).removeClass('d-none')

                        }else if (response.status == false) {
                            $("#booking-form").hide();
                            $("#submitBtn").hide();
                            if(lang == 'en'){
                                $("#successForm").find(".alert").text(response.message).removeClass('d-none')
                            }else{
                                $("#successForm").find(".alert").text(response.message_ur).removeClass('d-none')
                            }
                            // $("#successForm").find(".alert").text(response.message).removeClass('d-none')


                            dAte = '<p class="no-data">'+response.message+'</p>';

                        }
                        $("#date-listing").html(dAte);
                    }
                    // if (type == 'get_date') {

                    //     var dAte = '';

                    //     if (response.status) {
                    //         //  console.log("response",response)
                    //         var coulmtoShow = response.data.columnToShow;




                    //         //  console.log("coulmtoShow",coulmtoShow)
                    //         var count = 1;
                    //         $.each(response.data.date, function(key, item) {


                    //             if (coulmtoShow >= count ) {
                    //                 dAte += `<div class="col col-lg-3 col-md-7 date-enable-n date-enable-${item.venue_address_id}">
                    //                     <div class="card text-center h-60 py-2 shadow-sm slot-selection" data-id="${item.venue_address_id}">
                    //                         <img src="${item.flag_path}" alt="Flag Image">
                    //                         <div class="card-body px-0">
                    //                             <h5 class="card-title title-binding">${convertDateToCustomFormat(item.venue_date)}</h5>
                    //                         </div>
                    //                     </div>
                    //                 </div>`;
                    //             }


                    //             count++;


                    //         })

                    //     } else {
                    //         dAte = '<p class="no-data"> No Data Found </p>';
                    //     }
                    //     nextBtn.find('b').text(defaultText)
                    //     $("#date-listing").html(dAte);


                    // }

                    // if (type == 'get_slots') {
                    //     var html = '';
                    //     if (response.selfie) {
                    //         // $("#selfie_required").val('yes')
                    //         $("#make-selfie-area").show();
                    //     } else {
                    //         $("#make-selfie-area").hide();
                    //         // $("#selfie_required").val('no')
                    //     }
                    //     $("#timezone").val(response.timezone).trigger("change");

                    //     if (response.status) {
                    //         $.each(response.slots, function(key, item) {
                    //             html += `<div class="col col-lg-3 col-md-6">
                    //             <div class="card text-center h-10 py-0 shadow-sm slot-capture checkSlot" data-id="${item.id}">

                    //                 <div class="card-body px-0">
                    //                     <h5 class="card-title title-binding">${convertTimeTo12HourFormat(item.slot_time)}</h5>
                    //                     <img class="load-img" src="{{ asset('assets/sm-loader.gif') }}" style="display:none">

                    //                 </div>
                    //             </div>
                    //             </div>`;
                    //         });
                    //         $("#slot-information-user").find('label').text("Your Current Timezone:" + response
                    //             .timezone);

                    //         $("#timezone-hidden").val(response.timezone)
                    //         $("#slot-listing").html(html).find(".loader").hide();
                    //         $(".confirm").show();
                    //         $(".back").show();
                    //         nextBtn.find('b').text(defaultText)
                    //     } else {
                    //         $("#slot-listing").html("<h1>" + response.message + "</h1>");
                    //         $(".confirm").hide();
                    //         $(".back").show();
                    //         nextBtn.find('b').text('error')
                    //         setTimeout(() => {
                    //             nextBtn.find('b').text(defaultText)
                    //         }, 2500);
                    //     }


                    // }
                    nextBtn.find('span').hide()
                    var oldTitle = $("#remeber-steps-app").val();

                    nextBtn.parents(".row").fadeOut("slow", function() {

                        $(this).next(".row").fadeIn();
                        $(this).next(".row").find('.head>label').text(oldTitle)

                    });


                }
            });


        }
    </script>
    <script>
        $(document).ready(function() {
            $("#myalert").addClass('d-none')
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
                        $("#errors").html('');
                        $this.find('span').show()
                        $this.find('b').text(successText)
                        // stopCamera();
                        setTimeout(() => {
                            $this.find('b').text(defaultText)
                            $("#wizardRow").fadeOut(300);
                            $("#successForm").fadeOut(300);
                        }, 1000);
                        // 'thankyou-page

                        $("#loader").hide();
                        window.location.href = response.redirect_url;



                    },
                    error: function(error, xhr) {

                        console.log("error", error)

                        if (error.status == 406) {
                            console.log("error", error.responseJSON.message)
                            $("#myalert").html(error.responseJSON.message).removeClass('d-none');
                            // $("#errors").html(error.responseJSON.message).show();
                        }
                        $this.find('b').text(defaultText)
                        if (error.responseJSON || error.responseJSON.errors) {

                            $this.find('b').text(defaultText)
                            $this.find('span').hide()
                            if (error.responseJSON.status == false) {

                                $this.find('b').text('Opps Error..')
                                setTimeout(() => {
                                    $this.find('b').text(defaultText)
                                }, 2000);
                            }
                            var errors = error.responseJSON.errors;
                            $("#myalert").html(error.responseJSON.message).removeClass('d-none');
                            // $("#errors").html(error.responseJSON.message);

                            // Clear any existing error messages
                            // $('.alert-danger').remove();
                            $(".error").remove();
                            $.each(errors, function(field, messages) {

                                var inputElement = $('[name="' + field + '"]');
                                inputElement.addClass('is-invalid');
                                if (field == 'country_code') {
                                    $("#countryCodeDiv").find('.error').remove();
                                    $("#countryCodeDiv").last().append(
                                        '<div class="error ' + field + '">' +
                                        messages.join('<br>') + '</div>');
                                    //  inputElement.before();
                                } else if (field == 'otp-verified') {
                                    inputElement.after('<div class="error ' + field +
                                        '">Submit your OTP to get verified</div>');
                                } else {
                                    inputElement.after('<div class="error ' + field +
                                        '">' + messages.join('<br>') + '</div>');
                                }


                            });
                        }
                    }
                });
            });
        });


        $(".change-timezone").change(function() {

            $this = $(this);
            var timezone = $this.find("option:selected").val();
            var id = $("#slot-information-user").attr('data-id');
            $.ajax({
                url: "{{ route('get-slots-timezone') }}",
                type: 'POST',
                data: {
                    timezone: timezone,
                    id: id,

                },
                success: function(response) {

                    var html = '';

                    if (response.status) {
                        $.each(response.slots, function(key, item) {
                            html += `<div class="col col-lg-3 col-md-6">
                                <div class="card text-center h-10 py-0 shadow-sm slot-capture checkSlot" data-id="${item.id}">

                                    <div class="card-body px-0">
                                        <h5 class="card-title title-binding">${convertTimeTo12HourFormat(item.slot_time)}</h5>
                                        <img class="load-img" src="{{ asset('assets/sm-loader.gif') }}" style="display:none">

                                    </div>
                                </div>
                                </div>`;
                        });

                        $("#slot-information-user").find('label').text("Your Current Timezone:" +
                            response.timezone);
                        $("#timezone-hidden").val(response.timezone)
                        $("#myalert").html(html).removeClass('d-none');
                        $(".confirm").show();
                        $(".back").show();
                    } else {
                        $("#myalert").html("<h1>" + response.message + "</h1>").removeClass('d-none');
                        $(".confirm").hide();
                        $(".back").show();

                    }


                },
                error: function(xhr) {
                    // email-contaniner
                    // $("#otpVerifiedMessage").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);
                    // $("#mobile-number").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);

                }
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
                            $("#error").addClass('danger').text("We are unable to detect a face. It look like this is something object or other. Please retry again.")
                            $("#submitBtn").hide();
                        }

                    }
                });
            });

        });
    </script>

    <script>
        document.title = "Book Dua Meeting | KahayFaqeer.org";
        $(document).ready(function() {

             $('#mobile').on('input', function() {
                 // Get the value of the phone input
                 let phoneNumber = $(this).val();

                 // Remove any non-digit characters (e.g., spaces, dashes)
                 phoneNumber = phoneNumber.replace(/\D/g, '');

                 // Check if the phone number has reached 10 digits
                 if (phoneNumber.length === 10) {
                      $("#submitBtn").show();
                      $("#mobile-number").find('p').text('');
                     // $("#opt-form-confirm").fadeIn(500);
                     // $("#mobile-number").removeClass('col-lg-7').addClass('col-lg-5');
                     $("#mobile-number").removeClass('col-lg-6').addClass('col-lg-6');
                 } else {
                     $("#submitBtn").hide();
                    //  $("#opt-form-confirm").fadeOut(500);
                    $("#mobile-number").find('p').text('Please enter 10 digit Number').addClass('error');
                     $("#mobile-number").removeClass('col-lg-6').addClass('col-lg-6');
                    // $("#mobile-number").removeClass('col-lg-5').addClass('col-lg-7');
                 }
             });
            $("#sendOtp").click(function() {

                $this = $(this);

                var loadingText = $this.attr('data-loading');
                var successText = $this.attr('data-success');
                var defaultText = $this.attr('data-default');

                $this.find('span').show()
                $this.find('label').text(loadingText)



                $.ajax({
                    url: "{{ route('send-otp') }}",
                    type: 'POST',
                    data: {
                        country_code: $("#country_code").val(),
                        mobile: $("#mobile").val(),
                        email: $("#email").val(),

                    },
                    success: function(response) {
                        $this.find('label').text(successText)
                        $this.find('span').hide()
                        // setTimeout(() => {
                        //     $this.find('label').text(defaultText)
                        // }, 2500);

                        $("#opt-form").show();
                        $("#submitBtn").hide();
                        $("#otpVerifiedMessage").find('p').removeClass('text-danger').addClass(
                            'text-success').text(response.message);
                        $this.find('label').text("Resend")

                    },
                    error: function(xhr) {

                        $this.find('span').hide()
                        $this.find('label').text(defaultText)
                        $("#otpVerifiedMessage").find('p').removeClass('text-success').addClass(
                            'text-danger').text(xhr.responseJSON.message);
                        // $("#email-contaniner").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);
                        // $("#mobile-number").find('p').removeClass('text-success').addClass('text-danger').text(xhr
                        //     .responseJSON.message);

                    }
                });
            })

            $("#submit-otp").click(function() {
                $this = $(this);

                var loadingText = $this.attr('data-loading');
                var successText = $this.attr('data-success');
                var defaultText = $this.attr('data-default');

                $this.find('span').show()
                $this.find('label').text(loadingText)

                $("#opt-form").show();

                $("#submitBtn").hide();
                $.ajax({
                    url: "{{ route('verify-otp') }}",
                    type: 'POST',
                    data: {
                        otp: $("#otp").val()
                    },
                    success: function(response) {
                        $this.find('label').text(successText)
                        $this.find('span').hide()
                        $("#loader-otp2").hide();
                        $("#opt-form-confirm").hide();
                        $("#submitBtn").show(); // Display a success message
                        $("#opt-form").hide();
                        $("#otpVerifiedMessage").find('p').removeClass('text-danger').addClass(
                            'text-success').text('One-time password (OTP) Verified');
                        // $("#mobile-number").find('p').addClass('text-success').text('Mobile Number Verified')
                        $("#otp-verified").val('verified');
                        // You can proceed with form submission here
                    },
                    error: function(xhr) {
                        $this.find('span').hide()
                        $this.find('label').text(defaultText)
                        $("#otp-verified").val('');
                        $("#opt-form").find('p').addClass('text-danger').text(xhr.responseJSON
                            .error);
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

        function stopCamera() {
            var startCameraButton = $('#start-camera');
            var captureSelfieButton = $('#capture-selfie');
            var restartCameraButton = $('#restart-camera');
            // Get the video element
            var video = document.getElementById('camera-view');

            // Pause the video
            if (video.srcObject) {
                var tracks = video.srcObject.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });
                video.srcObject = null;
            }

            // Hide the camera view
            $("#camera-view").hide();


            // Hide the "Capture Selfie" and "Restart Camera" buttons, and show the "Start Camera" button
            startCameraButton.show();
            captureSelfieButton.hide();
            restartCameraButton.hide();
        }



        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "60000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        const mobileInput = document.getElementById('mobile-number');
        const videoElement = document.getElementById('camera-preview');
        let stream;

        // Function to start camera
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    videoElement.srcObject = mediaStream;
                })
                .catch(function(error) {
                    console.error('Error accessing camera:', error);
                });
        }

        function capturePicture() {
            // Check if the video element is playing and ready to capture
            if (!videoElement.paused && videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement('canvas');
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;

                // Wait for the video to render on the canvas
                videoElement.addEventListener('canplay', function() {
                    // Draw the video frame onto the canvas
                    canvas.getContext('2d').drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                    // Stop camera stream
                    stream.getTracks().forEach(track => track.stop());

                    // Convert canvas to image data URL
                    const imageDataURL = canvas.toDataURL('image/png');

                    // Set the image data URL as the value of an input field
                    $("#image-input").val(imageDataURL);

                    // Set the image data URL as the src of an <img> tag for display
                    $("#showhere").attr('src', imageDataURL);
                });
            } else {
                // Handle the case where the video is not yet ready to capture
                console.error('Video is not ready or paused.');
            }
}

        // Event listener for typing in the mobile number field
        mobileInput.addEventListener('focusout', function(event) {
            if (mobileInput.value !== '') {
                startCamera()
                // Automatically capture picture after 3 seconds (adjust as needed)
                capturePicture()
                stopCamera();
            }
        });
    });
</script>
@endsection
