@extends('layouts.app')
@section('content')
    <style>
        .tr-overlay {
            width: 100%;
            background-color: #333;
            overflow: hidden;
            margin: 30px 0px;
            text-align: center;
        }

        .wrapper {
            height: auto;
        }

        .tr-overlay .container {
            color: white;
        }
    </style>

    <div class="row">
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
                <h5 class="card-title">Create Manual Booking</h5>
                <div class="tr-overlay">
                    <div class="wrapper">
                        <div class="container">

                            <h4> About Venue</h4>
                            <p>{{ $venueAddress->thripist->name }} </p>
                            <p>{{ $venueAddress->venue_date }} </p>
                            <p>{{ $venueAddress->address }} </p>
                            <p>{{ $venueAddress->venue->country_name }} </p>
                            <p> <span class="badge bg-success">
                                    {{ $venueAddress->type == 'on-site' ? 'Physical' : 'Online' }} </span> </p>
                        </div>

                    </div>
                </div>



                {!! Form::open([
                    'route' => 'booking.submit',
                    'method' => 'POST',
                    'class' => 'row g-3',
                    'enctype' => 'multipart/form-data',
                ]) !!}
                <input type="hidden" name="from" value="admin">
                {{-- Just for Tracking Purpose --}}
                <input type="hidden" name="user_question" value="admin-side-booking">

                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Pick Dua Type</span>
                        <select class="form-control" name="dua_type" id="type_dua">
                            <option> -- Select Dua Option-- </option>
                            <option value="dum">Dum</option>
                            <option value="dua">Dua</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Pick Token</span>
                        <select class="form-control" name="slot_id" id="token_id">

                        </select>
                    </div>
                </div>

                {{-- <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">First Name</span>
                        {!! Form::text('fname', null, ['placeholder' => 'First Name', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Last Name</span>
                        {!! Form::text('lname', null, ['placeholder' => 'Last Name', 'class' => 'form-control']) !!}
                    </div>
                </div> --}}

                {{-- <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Email</span>
                        {!! Form::text('email', null, ['placeholder' => 'Email', 'class' => 'form-control']) !!}
                    </div>
                </div> --}}
                <div class="col-md-2">
                    <div class="input-group">

                        <select class="form-control js-states " name="country_code" id="country_code">
                            @foreach ($countries as $country)
                                <option value="{{ $country->phonecode }}">{{ $country->nicename }}
                                    (+{{ $country->phonecode }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Phone</span>
                        {!! Form::text('mobile', null, ['placeholder' => 'Phone', 'class' => 'form-control']) !!}
                    </div>
                </div>



                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Create Booking</button>
                </div>
                {!! Form::close() !!}
                <!-- End Browser Default Validation -->

            </div>
        </div>
    </div>
    <style>
        .select2-container--default .select2-selection--single {
            height: 36px;
        }
    </style>
@endsection
@section('page-script')
    <script>
        document.title = 'Manual Booking';
        $("#country_code").select2({
            placeholder: "Select country",
            allowClear: true
        });

        $("#type_dua").change(function() {

            var typeDua = $(this).find(':selected').val();
            var venueAddressId = "{{ $id  }}";

            console.log("typeDua",typeDua)
            console.log("venueAddressId",venueAddressId)

            $.ajax({
                url: "{{ route('get-slots') }}",
                type: 'POST',
                data: {
                    dua_option: typeDua,
                    venueId : venueAddressId,
                    _token: "{{ csrf_token() }}"
                },

                success: function(response) {
                    var options = '';

                    $.each(response.data , function(i,item){
                        options+=`<option value='${item.id}'> ${item.token_id} </option>`;

                    })
                    $("#token_id").html(options)

                    // You can proceed with form submission here
                },
                error: function(xhr) {

                }
            });

        });
    </script>
@endsection
