<div class="row justify-content-center form-business" style="display: none">

    <div class="col-lg-12 col-md-12">
        <div class="head mb-4">
            <h3 class="fw-bold text-center">{{ trans('messages.select-option') }}</h3>

            <label></label>
        </div>

        <p class="error d-none text-center alertBox">{{ trans('messages.select-option-card') }}</p>
        <!-- cards -->
        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="thripis1t-main">

            @foreach (['dua' => 'Dua', 'dum' => 'Dum'] as $key => $dua)
                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 col">
                    <div class="card text-center h-60  shadow-sm dua-section"
                        data-id="{{ $key }}" data-type="{{ $key }}">
                        <div class="card-body px-0">
                            <h5 class="card-title title-binding">{{ trans('messages.' . $key) }}</h5>
                            <p class="card-text">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @include('frontend.multistep.inc.buttons', ['buttonId' => 'select_dua_section'])

    </div>
</div>
