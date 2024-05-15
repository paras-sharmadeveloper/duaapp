<div class="row justify-content-center form-business" id="city-listing-main" style="display: none">
    <!-- col -->
    <div class="col-lg-12 col-md-12">
        <div class="head mb-4">
            <h3 class="fw-bold text-center">{{ trans('messages.select-city') }}</h3>
            <label></label>
        </div>
        <p class="error d-none text-center alertBox">{{ trans('messages.select-option') }}</p>
        <!-- cards -->
        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-5 border-bottom main-inner" id="city-listing" >

        </div>


        <!-- NEXT BUTTON-->
        @include('frontend.multistep.inc.buttons', ['buttonId' => 'city_section'])
        <!-- /NEXT BUTTON-->
    </div>
    <!-- /col -->
</div>
