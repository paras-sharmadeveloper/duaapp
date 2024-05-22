<button type="button" class="btn btn-info text-white float-start back mt-4 rounded-3">{{ trans('messages.back-btn') }}</button>


<button type="button" class="btn text-white float-end next mt-4 rounded-3 bg-color-info confirm" data-buttonId = "{{$buttonId }}"
    data-loading="{{ trans('messages.loading-btn') }}..." data-success="{{ trans('messages.done-btn') }}"
    data-default="{{ trans('messages.next-btn') }}">
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
    </span>
    <b>{{ trans('messages.next-btn') }}</b>
</button>
