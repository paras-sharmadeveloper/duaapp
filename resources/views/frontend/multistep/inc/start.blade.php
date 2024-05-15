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
            <p>{{ $reasons ? $reasons->reason_english : '' }}</p>
            <p> {{ $reasons ? $reasons->reason_urdu : '' }}</p>
        </div>

    </div>
</div>
