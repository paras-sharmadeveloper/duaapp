
<div class="row justify-content-center" id="Lang-Section" style="display: none">
    <h3 class="text-center">{{ trans('messages.select-lang-head') }}</h3>

    <div class="col-lg-12 col-md-12">
        @foreach (['en' => 'English', 'ur' => 'اردو'] as $key => $lang)
            <div class="row row-cols-3 d-flex justify-content-center">
                <button type="button"
                    class="btn text-white float-end mt-4 rounded-3 bg-color-info language-selection"
                    data-lang="{{ route('book.show.newdua', [$key]) }}">
                    </span>
                    <b> {{ $lang }} </b>

                </button>
                {{-- <button class="btn text-white float-end next mt-4 rounded-3 bg-color-info " id="startBooking"> Start Booking </button> --}}
            </div>
        @endforeach
    </div>
</div>
