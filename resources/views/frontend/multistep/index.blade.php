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
                        <img id="img" src="#" alt="Captured Image">

                </div>
                <div class="row justify-content-center pt-0 p-4" id="wizardRow"  @if (empty($locale)) style="display: none" @endif >
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
