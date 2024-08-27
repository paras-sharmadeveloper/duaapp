@extends('layouts.app')
@section('content')


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
                <h5 class="card-title">Special Token Booking </h5>

                <form action="{{route('manual.token.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend2">Pick Dua Type</span>
                                <select class="form-control" name="dua_type" id="type_dua" required>
                                    <option value=""> -- Select Dua Option-- </option>
                                    <option value="dum">Dum</option>
                                    <option value="dua">Dua</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend2">Pick Token</span>
                                <select class="form-control" name="slot_id" id="token_id" required>

                                     @foreach($slots as $slot)
                                         @if($slot->type == 'special_token')
                                           <option value="{{ $slot->id }}">{{ $slot->token_id }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend2">Country</span>
                                <select class="form-control" name="country_code" id="country_cod1e" required>
                                    <option value="+91">India</option>
                                    <option value="+92">Pakistan</option>
                                    <option value="+971">UAE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend2">WhatsApp Mobile Number</span>
                                <input type="tel" class="form-control" name="phone" id="mobile" maxlength="10" required>
                            </div>
                        </div>
                    </div>




                    <div class="col-12 mt-4">
                        <button class="btn btn-primary" type="submit">Create Booking</button>
                    </div>


                </form>


                <!-- End Browser Default Validation -->

            </div>
        </div>
    </div>

    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Issued Tokens</h5>

                <form action="">
                    <table class="table-with-buttons table table-responsive cell-border">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Db Id</th>
                                <th>Date</th>
                                <th>CountryCode</th>
                                <th>Phone </th>
                                <th>User Image </th>
                                <th>Dua Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visitorList as $list)
                                @php
                                    $loclpath = '/sessionImages/' . date('d-m-Y') . '/';
                                @endphp
                                @php
                                    $localImage = '';
                                    $localImageStroage =
                                        'sessionImages/' . date('d-m-Y') . '/' . !empty($list->recognized_code)
                                            ? $list->recognized_code
                                            : '';
                                    if (
                                        !empty($list->recognized_code) &&
                                        !Storage::disk('public_uploads')->exists($localImageStroage)
                                    ) {
                                        $localImage = !empty($list->recognized_code) ? $list->recognized_code : '';
                                    }

                                @endphp
                                <tr>
                                    <td>
                                        @if (empty($list->action_at))
                                            <input type="checkbox" class="bulk-checkbox" data-id="{{ $list->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->created_at->format('d M Y H:i:s A') }}</td>
                                    <td>{{ $list->country_code }}</td>
                                    <td>{{ $list->phone }}</td>

                                    <td class="imgc">
                                         <img class="lightgallery" src="{{ $loclpath . $localImageStroage }}" />

                                    </td>
                                    <td>{{ ucwords($list->dua_type) }}</td>
                                    <td>
                                        @if (empty($list->action_at))
                                            <div class="row py-4 actionBtns">

                                                <button type="button" class="btn btn-success approve mb-3"
                                                    data-id="{{ $list->id }}" data-loading="Loading..." data-success="Done"
                                                    data-default="Approve">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                                        style="display:none">
                                                    </span>
                                                    <b>Approve ({{ ucwords($list->dua_type) }})</b>
                                                </button>

                                                <button type="button" class="btn  btn-danger disapprove"
                                                    data-id="{{ $list->id }}" data-loading="Loading..." data-success="Done"
                                                    data-default="Disapprove">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                                        style="display:none">
                                                    </span>
                                                    <b>Disapprove ({{ ucwords($list->dua_type) }})</b>
                                                </button>
                                            </div>
                                        @else
                                            <p> Action Taken
                                                @if($list->action_status)
                                                <span class="{{ ($list->action_status == 'approved')? 'btn btn-success btn-sm':'btn btn-danger btn-sm' }}">{{ $list->action_status}} </span>
                                                @endif
                                            </p>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>




                    <div class="col-12 mt-4">
                        <button class="btn btn-primary" type="submit">Create Booking</button>
                    </div>


                </form>


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
