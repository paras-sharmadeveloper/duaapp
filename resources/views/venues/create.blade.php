@extends('layouts.app')

@section('content')
    <div class="row">
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

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Select Country</span>
                         <select class="form-control" name="venue_id">
                            @foreach($countries as $country)
                            <option value="{{  $country->id }}"> {{  $country->country_name }}</option>
                            @endforeach
                         </select>
                    </div>

                </div>

                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Select Thripist</span>
                         <select class="form-control" name="therapist_id">
                            @foreach($therapists as $therapist)
                            <option value="{{  $therapist->id }}"> {{  $therapist->name }}</option>
                            @endforeach
                         </select>
                    </div>

                </div>

                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">State </span>
                        {!! Form::text('state', $venueAddress->state ?? '' , ['class' => 'form-control', 'placeholder' => 'state']) !!}

                    </div>
                </div>

                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">City </span>
                        {!! Form::text('city', $venueAddress->city ?? '' , ['class' => 'form-control', 'placeholder' => 'city']) !!}

                    </div>
                </div>

                
                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">Venue Addresses</span>

                        {!! Form::textarea('venue_addresses',$venueAddress->address ?? '', ['class' => 'form-control', 'placeholder' => 'Address','row' => 2]) !!}

                    </div>

                </div>

                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">Date </span>
                        {!! Form::date('venue_date', $venueAddress->venue_date ?? '' , ['class' => 'form-control', 'placeholder' => 'Date']) !!}

                    </div>
                </div>
                <div class="col-md-6 mt-4 ">
                    <div class="input-group">
                        <span class="input-group-text">starts at</span>
                        {!! Form::time('venue_starts',$venueAddress->slot_starts_at ?? '', [
                            'class' => 'form-control',
                            'placeholder' => 'Starts',
                        ]) !!}

                    </div>
                </div>
                <div class="col-md-6 mt-4">
                    <div class="input-group">
                        <span class="input-group-text">ends at</span>
                        {!! Form::time('venue_ends', $venueAddress->slot_ends_at ?? '', ['class' => 'form-control', 'placeholder' => 'ends']) !!}
                        
                    </div>
                </div>

                 

                <div class="form-group mt-4">
                    <label for="type">Type</label>
                    <div>
                        <input type="radio" id="on-site" name="type" value="on-site"
                            {{ isset($venueAddress) && $venueAddress->type === 'on-site' ? 'checked' : '' }} required>
                        <label for="on-site">On-site</label>
                    </div>
                    <div>
                        <input type="radio" id="virtual" name="type" value="virtual"
                            {{ isset($venueAddress) && $venueAddress->type === 'virtual' ? 'checked' : '' }} required>
                        <label for="virtual">Virtual</label>
                    </div>
                </div>
                @if (Route::currentRouteName() == 'venues.edit')
                <div class="form-check">
                    {!! Form::checkbox('update_slots', 'yes',null, ['class' => 'form-check-input', 'id' => 'checkbox_id']) !!}
                    <label class="form-check-label" for="checkbox_id">Check If you also want to Update Slots and Date </label>
                </div>
                @endif

                <button type="submit"
                    class="btn btn-primary mt-4">{{ isset($venue) ? 'Update Venue' : 'Create Venue' }}</button>
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
                venueAddresses.append('<div class="row g-3 mt-3">' + venuehtml + '</div>');
                $(".venue-addresses").find('.remove-address').removeClass('d-none');
            })


            venueAddresses.on("click", ".remove-address", function() {
                $(this).closest('.row').remove();
            });
        });
    </script>
@endsection
