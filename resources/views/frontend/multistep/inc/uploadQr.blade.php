<div class="row justify-content-center form-business" id="qr-listing" style="display: none">
        <!-- col -->
        <div class="col-lg-12 col-md-12">
            <div class="head mb-4">
                <h3 class="fw-bold text-center">{{ trans('messages.select-city') }}</h3>
                <label></label>
            </div>
            <p class="error d-none text-center alertBox">{{ trans('messages.select-option') }}</p>

            <div class="row row-cols-1 row-cols-lg-3 g-2 pb-5 border-bottom main-inner" id="qr-code-listing">
                <div class="card qr-code" data-id="get_city"  style="display: none"></div>

                <p id="cadr-1r" class="error"></p>
                <div id="reader"></div>

                <div class="card-1">
                    <h3>Upload Files</h3>
                    <div class="drop_box">
                    <header>
                        <h4>Select File here</h4>
                    </header>
                    <input type="file" hidden accept="*" id="qr-input-file" style="display:block;" name="QrFile">
                    <button class="btn">Choose File</button>
                    </div>
                    <p>{{ trans('messages.working_lady_note') }}</p>
                    {{-- <p> {{ trans('messages.workingLady_notRegister') }} <a href="{{ route('working.lady.show')}} ">Register</a></p> --}}

                </div>


            </div>

            @include('frontend.multistep.inc.buttons', ['buttonId' => 'qr_section'])
        </div>
    </div>


