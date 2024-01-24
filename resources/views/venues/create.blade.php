@extends('layouts.app')

@section('content')
    <style>
        .form-check-input {
            cursor: pointer;
        }

        .ellipsis {
            text-overflow: ellipsis;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
        }

        .apply-selection {
            display: none;
            width: 100%;
            margin: 0;
            padding: 5px 10px;
            border-bottom: 1px solid #ccc;
        }

        .apply-selection .ajax-link {
            display: none;
        }

        .checkboxes {
            margin: 0;
            display: none;
            border: 1px solid #ccc;
            border-top: 0;
        }

        .checkboxes .inner-wrap {
            padding: 5px 10px;
            max-height: 140px;
            overflow: auto;
        }

        .inner-wrap label {
            padding: 10px 20px;
            cursor: pointer;
        }

        .inline-label,
        .inline-input {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 0;
            /* Remove default margin-bottom */
        }
    </style>

    <div class="row mt-3">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('venues.index') }}"> <i
                        class="bi bi-skip-backward-circle me-1"></i> Back</a>
            </div>

        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">
                @if (Route::currentRouteName() == 'venues.edit')
                    <h5 class="card-title">Edit Venue</h5>

                    {!! Form::model($venueAddress, [
                        'route' => ['venues.update', $venueAddress->id],
                        'method' => 'PUT',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                @else
                    {!! Form::open(['route' => 'venues.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                    <h5 class="card-title">Create Venue</h5>

                    <form method="POST" action="{{ route('venues.store') }}">
                @endif
                
                <div class="row mt-3">
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroupPrepend2">Select Field Admin</span>
                            <select class="form-control" name="siteadmin_id">
                                @foreach ($siteAdmins as $siteadmin)
                                    <option value="{{ $siteadmin->id }}" @if (!empty($venueAddress) && $venueAddress->siteadmin_id == $siteadmin->id) selected @endif>
                                        {{ $siteadmin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">City </span>
                            <select name="city" class="form-control"> 
                                <option name="Lahore">Lahore </option>
                                <option name="Islamabad" >Islamabad</option>
                                <option name="Karachi" >Karachi</option>
                            </select>
                            {{-- {!! Form::text('state', $venueAddress->state ?? '', ['class' => 'form-control', 'placeholder' => 'state','id' =>'state_name','readonly'=>true ]) !!} --}}

                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Date </span>
                            {!! Form::date('venue_date', $venueAddress->venue_date ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Date',
                                'min' => date('Y-m-d'),
                            ]) !!}

                        </div>
                    </div>
 

                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Venue Addresses</span>

                            {!! Form::textarea('venue_addresses', $venueAddress->address ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Address',
                                'cols' => 5,
                                'rows' => 2,
                            ]) !!}

                        </div>

                    </div>
                </div>





                <div class="row mt-3">
                    


                    {{-- <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Slot Duration</span>
                            {!! Form::number('slot_duration', $venueAddress->slot_duration ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Add Slot Duration in Mint',
                            ]) !!}

                        </div>
                    </div> --}}
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 mt-4 ">
                        <div class="input-group">
                            <span class="input-group-text">Dua Slots</span>
                            {!! Form::number('dua_slots', $venueAddress->dua_slots ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Dua Slots',
                                'max' => 1000
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Dum Slots</span>
                            {!! Form::number('dum_slots', $venueAddress->dum_slots ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Sum Slots',
                                'min' => 1001,
                                'max' => 2000
                            ]) !!}

                        </div>
                    </div>
                </div>

                {{-- <div class="row mt-3">
                    <div class="col-md-6 mt-4 ">
                        <div class="input-group">
                            <span class="input-group-text">starts at (Morning)</span>
                            {!! Form::time('slot_starts_at_morning', $venueAddress->slot_starts_at_morning ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Starts',
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">ends at (Morning)</span>
                            {!! Form::time('slot_ends_at_morning', $venueAddress->slot_ends_at_morning ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'ends',
                            ]) !!}

                        </div>
                    </div>
                </div> --}}
                {{-- <div class="row mt-3">

                    <div class="col-md-6 mt-4 ">
                        <div class="input-group">
                            <span class="input-group-text">starts at (Evening)</span>
                            {!! Form::time('slot_starts_at_evening', $venueAddress->slot_starts_at_evening ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Starts',
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">ends at (Evening)</span>
                            {!! Form::time('slot_ends_at_evening', $venueAddress->slot_ends_at_evening ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'ends',
                            ]) !!}

                        </div>
                    </div>
                </div> --}}

                <div class="row mt-3">
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="" name="is_recurring[sunday]"
                                @if (!empty($venueAddress) && $venueAddress->is_sunday == 1) checked @endif>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Every Sunday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="" name="is_recurring[monday]"
                                @if (!empty($venueAddress) && $venueAddress->is_monday == 1) checked @endif>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Every Monday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="tuesday" name="is_recurring[tuesday]"
                                @if (!empty($venueAddress) && $venueAddress->is_tuesday == 1) checked @endif>
                            <label class="form-check-label" for="tuesday">Every Tuesday</label>
                        </div>
                    </div>

                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wednesday"
                                name="is_recurring[wednesday]" @if (!empty($venueAddress) && $venueAddress->is_wednesday == 1) checked @endif>
                            <label class="form-check-label" for="wednesday">Every Wednesday</label>
                        </div>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="thursday" name="is_recurring[thursday]"
                                @if (!empty($venueAddress) && $venueAddress->is_thursday == 1) checked @endif>
                            <label class="form-check-label" for="thursday">Every Thursday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="friday" name="is_recurring[friday]"
                                @if (!empty($venueAddress) && $venueAddress->is_friday == 1) checked @endif>
                            <label class="form-check-label" for="tuesday">Every Friday</label>
                        </div>
                    </div>

                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="saturday" name="is_recurring[saturday]"
                                @if (!empty($venueAddress) && $venueAddress->is_saturday == 1) checked @endif>
                            <label class="form-check-label" for="tuesday">Every Saturday</label>
                        </div>
                    </div>



                </div>
                <div class="row mt-3">
                    {{-- <div class="col-md-2 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="selfie_verification" name="selfie_verification"
                                @if (!empty($venueAddress) && $venueAddress->selfie_verification == 1) checked @endif>
                            <label class="form-check-label" for="tuesday">Selfie Verification</label>
                        </div>
                    </div> --}}
                    <div class="col-md-4 mt-4">
                        <label>Recurring Till How many Month ? </label>
                   
                        <div class="input-group">
                            
                            {!! Form::number('recurring_till', $venueAddress->recurring_till ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'ends',
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label>User Rejoin After Days? </label>
                        <div class="input-group">
                            {{-- <span class="input-group-text">User Rejoin After Days? </span>  --}}
                            {!! Form::number('rejoin_venue_after', $venueAddress->rejoin_venue_after ?? 0, [
                                'class' => 'form-control',
                                'placeholder' => 'rejoin_venue_after',
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label>Slots Appear Before Hours ?</label>
                        <div class="input-group">
                            {{-- <span class="input-group-text">Slots Appear Before Hours ?</span> --}}
                            {!! Form::number('slot_appear_hours', $venueAddress->slot_appear_hours ?? 0, [
                                'class' => 'form-control',
                                'placeholder' => 'slot_appear_hours',
                            ]) !!}

                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    {{-- <div class="col-md-4  mt-4">
                        <label for="type">Type</label>
                        <div>
                            <input type="radio" id="on-site" name="type" value="on-site"
                                {{ isset($venueAddress) && $venueAddress->type === 'on-site' ? 'checked' : '' }} required>
                            <label for="on-site">Physical (On Site)</label>
                        </div>
                        <div>
                            <input type="radio" id="virtual" name="type" value="virtual"
                                {{ isset($venueAddress) && $venueAddress->type === 'virtual' ? 'checked' : '' }} required>
                            <label for="virtual">Online (Virtual)</label>
                        </div>
                    </div> --}}
                    <div class="col-md-6 mt-4">
                        @php
                            $savedCountries = isset($venueAddress) ? json_decode($venueAddress->venue_available_country) : [];
                        @endphp
                        <label> Venue Available Country </label>
                        <div class="wrapper">
                            <button class="form-control toggle-next ellipsis" type="button">
                                @if (is_array($savedCountries) && count($savedCountries) == 239)
                                    All Countries Selected
                                @else
                                    Select Countries
                                @endif
                            </button>
                            <div class="checkboxes" id="checkboxes" data-id="countries">

                                <input type="text" class="form-control" id="search-in" placeholder="search">

                                <div class="inner-wrap">
                                    <div class="main-list">
                                        <label>
                                            <input type="checkbox" value="0" class="ckkBox all"
                                                @if (is_array($savedCountries) && count($savedCountries) == 239) checked @endif />
                                            <span>All Countries</span>
                                        </label>
                                    </div>
                                    @foreach ($venueCountry as $country)
                                        <div class="main-list">
                                            <label>
                                                <input type="checkbox" value="{{ $country->id }}" class="ckkBox val"
                                                    @if (is_array($savedCountries) && in_array($country->id, $savedCountries)) checked @endif
                                                    name="venue_available_country[]" />
                                                <span>{{ $country->nicename }}</span>
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-md-6 mt-4">
                        <label>Status Page Note</label>
                        <div class="input-group"> 
                            <textarea name="status_page_note" 
                            class="form-control"
                            id="status_page_note" cols="10" rows="5" placeholder="User booking Status page note">{{ $venueAddress->status_page_note ?? '' }}</textarea>
                            
 
                        </div>
                    </div>

                    

                  

                    {{-- <div class="col-md-4 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Video Room Name</span>
                            <input type="text" id="video_room" class="form-control" name="video_room"
                                value="{{ $venueAddress->room_name ?? '' }}" placeholder = 'Enter Vedio Room Name'>


                        </div>
                    </div> --}}
                </div>
                @if (Route::currentRouteName() == 'venues.edit')
                    <div class="form-check">
                        {!! Form::checkbox('update_slots', 'yes', null, ['class' => 'form-check-input', 'id' => 'checkbox_id']) !!}
                        <label class="form-check-label" for="checkbox_id">Check If you also want to Update Slots and Date
                        </label>
                    </div>
                @endif
                @if (Route::currentRouteName() == 'venues.edit')
                    <button type="submit" class="btn btn-primary mt-4">{{ 'Update' }}</button>
                @else
                    <button type="submit" class="btn btn-primary mt-4">{{ 'Create' }}</button>
                @endif
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
        var CurrentPage = "{{ Route::currentRouteName() }}"
        $(document).ready(function() {
            var addAddressButton = $(".add-address");
            var venueAddresses = $(".venue-addresses");
            var venuehtml = $("#venue-htm").html();
            $(document).on('click', ".add-address", function() {
                venueAddresses.append('<div class="row mt-3 g-3 mt-3">' + venuehtml + '</div>');
                $(".venue-addresses").find('.remove-address').removeClass('d-none');
            })


            venueAddresses.on("click", ".remove-address", function() {
                $(this).closest('.row mt-3').remove();
            });
        });

        $(function() {

            setCheckboxSelectLabels();

            $('.toggle-next').click(function() {
                $(this).next('.checkboxes').slideToggle(400);
            });

            $('.ckkBox').change(function() {
                $this = $(this);


                toggleCheckedAll(this);
                setCheckboxSelectLabels();
            });

            $('.all').change(function() {
                $this = $(this);
                if ($this.is(':checked')) {

                    $(".inner-wrap .ckkBox").each(function(item, key) {
                        if ($(this).hasClass('val')) {
                            $(this).prop('checked', true)
                        }

                    })

                } else {
                    $(".inner-wrap .ckkBox").each(function(item, key) {
                        if ($(this).hasClass('val')) {
                            $(this).prop('checked', false)
                        }

                    })
                }
                setCheckboxSelectLabels();
            });

        });

        function setCheckboxSelectLabels(elem) {
            var wrappers = $('.wrapper');
            $.each(wrappers, function(key, wrapper) {
                var checkboxes = $(wrapper).find('.ckkBox');
                var label = $(wrapper).find('.checkboxes').attr('data-id');
                var prevText = '';
                $.each(checkboxes, function(i, checkbox) {
                    var button = $(wrapper).find('button');
                    if ($(checkbox).prop('checked') == true) {
                        var text = $(checkbox).next().html();
                        var btnText = prevText + text;
                        var numberOfChecked = $(wrapper).find('input.val:checkbox:checked').length;
                        if (numberOfChecked >= 8) {
                            btnText = numberOfChecked + ' ' + label + ' selected';
                        }
                        if (numberOfChecked == 239) {
                            btnText = 'All Countries selected';
                        }
                        $(button).text(btnText);
                        prevText = btnText + ', ';
                    }
                });
            });
        }

        function toggleCheckedAll(checkbox) {
            var apply = $(checkbox).closest('.wrapper').find('.apply-selection');
            apply.fadeIn('slow');

            var val = $(checkbox).closest('.checkboxes').find('.val');
            var all = $(checkbox).closest('.checkboxes').find('.all');
            var ckkBox = $(checkbox).closest('.checkboxes').find('.ckkBox');

            if (!$(ckkBox).is(':checked')) {
                $(all).prop('checked', true);
                return;
            }

            if ($(checkbox).hasClass('all')) {
                $(val).prop('checked', false);
            } else {
                $(all).prop('checked', false);
            }
        }

        $("#search-in").on("keyup", function() {
            var text = $(this).val().toLowerCase();

            console.log("text", text);

            $(".main-list > label").each(function() {
                var title = $(this).text().toLowerCase(); // Get the text of the whole label

                if (!title.includes(text)) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    </script>
    <script>
        if (CurrentPage == 'venues.create') {
            $(".inner-wrap .ckkBox").each(function(item, key) {

                $(this).prop('checked', true) 
            })
        }
        $("#combination_id").change(function(){
            var state = $(this).find(':selected').attr('data-state');
            var city = $(this).find(':selected').attr('data-city');
            $("#city_name").val(city);
            $("#state_name").val(state);

        });
        $("#venue_id").change(function(){
            var id = $(this).find(":selected").val(); 
            $.ajax({
                url: "{{ route('get-states') }}",
                type: 'GET',
                data: {
                    venue_id : id
                }, 
                success: function(response) { 
                    var options = '<option>Select Combination</option>'; 
                    $.each(response,function(i,item){
                        options+=`<option value='${item.id}'  
                        data-state='${item.state_name}'
                        data-city='${item.city_name}'
                        >${item.combination_name} (${item.columns_to_show}) </option>`; 
                    })
                    $("#combination_id").html(options)
                     
                },
                error: function(error) {
                     
                    
                }
            });

        })
        document.title = (CurrentPage == 'venues.edit') ? 'Edit Venue Booking' : 'Create Venue Booking';
    </script>
@endsection
