@extends('layouts.guest')
@section('content')
    @include('frontend.multistep.inc.style')
    <section>
        <!-- container -->
        <div class="container">
            <!-- main content -->
            <div class="main-content">

                <div class="d-flex justify-content-center py-4">
                    <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                        <img src="https://kahayfaqeer.org/assets/kahe-faqeer-white-1.png" alt="">
                    </a>
                </div>

                <div class="d-flex justify-content-center py-4">

                    <video id="video" autoplay style="display: none;"></video>
                    <img id="img" src="#" alt="Captured Image" style="display: none;">

                </div>
                <div class="row justify-content-center pt-0 p-4" id="wizardRow"
                    @if (empty($locale)) style="display: none" @endif>
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
                @if (empty($locale))
                    @include('frontend.multistep.inc.lang')
                @else
                    @include('frontend.multistep.inc.start')
                    @include('frontend.multistep.inc.choosetype')
                    @include('frontend.multistep.inc.city')
                    @include('frontend.multistep.inc.uploadQr')
                    @include('frontend.multistep.inc.selectdua')
                    @include('frontend.multistep.inc.submit')
                @endif



            </div>
            <div id="remeber-steps" class="d-none">
                <input type="hidden" name="remeber-steps-app" id="remeber-steps-app" data-step="1">
            </div>
            <!-- /main content -->
        </div>
        <!-- /container -->
    </section>

    <div class="modal" id="modal-loading" data-backdrop="static" style="margin-top: 50%">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loading-spinner mb-2"></div>
                    <div id="textPop"><b>Please Wait... Token Booking In Process...</b></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-loading2" data-backdrop="static" style="margin-top: 50%">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loading-spinner mb-2"></div>
                    <div id="textPop"><b>Please Wait...</b></div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 2px solid indigo;
            border-radius: 50%;
            border-top-color: #0001;
            display: inline-block;
            animation: loadingspinner .7s linear infinite;
        }

        @keyframes loadingspinner {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }
    </style>

    <!-- /section -->
@endsection
@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        var translations = {!! json_encode(trans('messages')) !!};

        var lang = "{{ $locale }}";
    </script>
    @include('frontend.multistep.inc.script')
@endsection
