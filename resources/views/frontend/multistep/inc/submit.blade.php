<div class="row justify-content-center py-5 form-business" style="display:none">

    <div class="head mb-4">
        {{-- <h3 class="fw-bold text-center">Final</h3> --}}
        <label></label>
    </div>
    <!-- col -->

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
                <input type="hidden" name="selectionType" id="selection_type" value="">
                <input type="hidden" name="city" id="citySelection" value="">
                <input type="hidden" name="working_lady_id" id="working_lady_id" value="">


                <input type="hidden" name="lang" id="lang" value="{{ $locale }}">
                <input type="hidden" name="captured_user_image" id="image-input" value="">
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



                <input type="hidden" name="timezone" id="timezone-hidden">

                <div class="disclaimer">
                    <p style="font-size:10px">
                        {{ trans('messages.disclaimer') }}

                    </p>
                </div>


        </div>

        {{-- <input type="hidden" name="selfie_required" id="selfie_required" value="yes"> --}}




        <!-- NEXT BUTTON-->
        <button type="button"
            class="btn btn-dark text-white float-start back rounded-3">{{ trans('messages.back-btn') }}</button>

        <button type="submit" id="submitBtn"
            class="btn text-white float-end submit-button rounded-3 bg-color-info" type="submit"
            data-loading="{{ trans('messages.submiting-btn') }}..."
            data-success="{{ trans('messages.done-btn') }}" data-default="Finish">
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
