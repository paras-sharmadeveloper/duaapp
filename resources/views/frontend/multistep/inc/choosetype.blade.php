
<div class="row justify-content-center form-business aa" id="cardSection" style="display:none">
    <div class="col-lg-12 col-md-12">
        <div class="head mb-4">
            <h3 class="fw-bold text-center">Select Your Type</h3>

            <label></label>
        </div>

        <p class="error d-none text-center alertBox">Select Your Type</p>
        <!-- cards -->
        <div class="row row-cols-1 row-cols-lg-3 g-4 pb-2 border-bottom" id="">

            @foreach (['normal_person' => 'Normal Person', 'working_lady' => 'Working Lady'] as $key => $item)
                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 col">
                    <div class="card text-center h-60  shadow-sm {{ $key }}"
                        data-id="{{ $key }}">
                        <div class="card-body px-0">
                            <h5 class="card-title title-binding">{{ $item }}</h5>
                            <p class="card-text">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @include('frontend.multistep.inc.buttons', ['buttonId' => 'choose_type_section'])


    </div>
</div>
