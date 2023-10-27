@extends('layouts.app')

@section('content')
<style>
    .form-check-input{
        cursor: pointer;
    }
    </style>

    <div class="row mt-3">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> <i
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
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text select-2" id="inputGroupPrepend2">Select Country</span>
                        <select class="form-control" name="venue_id">
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if (!empty($venueAddress) && $venueAddress->venue_id == $country->id) selected @endif>
                                    {{ $country->country_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Select Thripist</span>
                        <select class="form-control" name="therapist_id">
                            @foreach ($therapists as $therapist)
                                <option value="{{ $therapist->id }}" @if (!empty($venueAddress) && $venueAddress->therapist_id == $therapist->id) selected @endif>
                                    {{ $therapist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                </div>
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
                        <span class="input-group-text">State </span>
                        {!! Form::text('state', $venueAddress->state ?? '', ['class' => 'form-control', 'placeholder' => 'state']) !!}

                    </div>
                </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">City </span>
                            {!! Form::text('city', $venueAddress->city ?? '', ['class' => 'form-control', 'placeholder' => 'city']) !!}

                        </div>
                    </div>


                    <div class="col-md-6 mt-4">
                        <div class="input-group">
                            <span class="input-group-text">Venue Addresses</span>

                            {!! Form::textarea('venue_addresses', $venueAddress->address ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Address',
                                'cols' => 5,
                                'rows' => 2
                            ]) !!}

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
                            <span class="input-group-text">Slot Duration</span>
                            {!! Form::number('slot_duration', $venueAddress->slot_duration ?? '', [
                                'class' => 'form-control',
                                'placeholder' => 'Add Slot Duration in Mint',
                            ]) !!}

                        </div>
                    </div>
                </div>

                <div class="row mt-3">
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
                </div>
                <div class="row mt-3">

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
                </div>

                <div class="row mt-3">
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="" name="is_recurring[sunday]"
                            
                            @if(!empty($venueAddress) && $venueAddress->is_sunday == 1)
                             checked 
                            @endif
                            >
                            <label class="form-check-label" for="flexSwitchCheckDefault">Every Sunday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="" name="is_recurring[monday]"
                            @if(!empty($venueAddress) && $venueAddress->is_monday == 1)
                             checked 
                            @endif
                            
                            >
                            <label class="form-check-label" for="flexSwitchCheckDefault">Every Monday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="tuesday" name="is_recurring[tuesday]"
                            @if(!empty($venueAddress) && $venueAddress->is_tuesday == 1) checked @endif
                            >
                            <label class="form-check-label" for="tuesday">Every Tuesday</label>
                        </div>
                    </div>

                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wednesday"
                                name="is_recurring[wednesday]"
                                @if(!empty($venueAddress) && $venueAddress->is_wednesday == 1) checked @endif    
                            >
                            <label class="form-check-label" for="wednesday">Every Wednesday</label>
                        </div>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="thursday"
                                name="is_recurring[thursday]"
                                @if(!empty($venueAddress) && $venueAddress->is_thursday == 1) checked @endif    
                                >
                            <label class="form-check-label" for="thursday">Every Thursday</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="friday" name="is_recurring[friday]"
                            @if(!empty($venueAddress) && $venueAddress->is_friday == 1) checked @endif 
                            >
                            <label class="form-check-label" for="tuesday">Every Friday</label>
                        </div>
                    </div>

                    <div class="col-md-3 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="saturday"
                                name="is_recurring[saturday]"  @if(!empty($venueAddress) && $venueAddress->is_saturday == 1) checked @endif>
                            <label class="form-check-label" for="tuesday">Every Saturday</label>
                        </div>
                    </div>

                    

                </div>
                <div class="row">
                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">Recurring Till How many Month ? </span>
                        {!! Form::number('recurring_till', $venueAddress->recurring_till ?? '', [
                            'class' => 'form-control',
                            'placeholder' => 'ends',
                        ]) !!}

                    </div>
                </div>
                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">User Rejoin After Days? </span>
                        {!! Form::number('rejoin_venue_after', $venueAddress->rejoin_venue_after ?? '', [
                            'class' => 'form-control',
                            'placeholder' => 'rejoin_venue_after',
                        ]) !!}

                    </div>
                </div>
            </div>




                <div class="row mt-3">
                <div class="col-md-6  mt-4">
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
                </div>

                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">Video Room Name</span>
                        {!! Form::text('video_room', $venueAddress->room_name ?? '', [
                            'class' => 'form-control',
                            'placeholder' => 'Enter Vedio Room Name',
                        ]) !!}

                    </div>
                </div>
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
    </script>
@endsection
