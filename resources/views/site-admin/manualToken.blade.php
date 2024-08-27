@extends('layouts.app')
@section('content')
<style>
    .readonly{
    color: #c5c1c1;

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
                <h5 class="card-title">Special Token Booking </h5>

                <form action="{{route('manual.token.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend2">Dua Type</span>
                                <input type="text" readonly value="dua"  class="form-control readonly" name="dua_type" id="type_dua" maxlength="10" required>

                                {{-- <select class="form-control" name="dua_type" id="type_dua" required>
                                    <option value=""> -- Select Dua Option-- </option>
                                    <option value="dum">Dum</option>
                                    <option value="dua">Dua</option>
                                </select> --}}
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
                                <span class="input-group-text" id="inputGroupPrepend2">Country Pakistan</span>

                                <input type="text" readonly value="+92"  class="form-control readonly" name="country_code" id="country_cod1e">

                                {{-- <select class="form-control" name="country_code" id="country_cod1e" required>
                                    <option value="+91">India</option>
                                    <option value="+92">Pakistan</option>
                                    <option value="+971">UAE</option>
                                </select> --}}
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
                                    Token Number
                                </th>
                                <th>Db Id</th>
                                <th>Date</th>
                                <th>CountryCode</th>
                                <th>Phone </th>
                                <th>Dua Type</th>
                                <th>Url</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visitorList as $list)

                                <tr>
                                    <td>{{ $list->booking_number }}</td>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->created_at->format('d M Y H:i:s A') }}</td>
                                    <td>{{ $list->country_code }}</td>
                                    <td>{{ $list->phone }}</td>
                                    <td>{{ ucwords($list->dua_type) }}</td>
                                    <td>
                                        <a href="{{route('booking.status',$list->booking_uniqueid)}}" target="_blank">Token URL</a>
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
