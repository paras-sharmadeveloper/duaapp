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



        .multiselect input[type="checkbox"] {
            /* display: none; */
        }

        .multiselect label {
            display: block;
            text-indent: -1.2em;
            padding: 0.2em 0 0.2em 1.2em;

        }

        .multiselect-on {
            color: #ffffff;
            background-color: #0E8EFF;
        }

        .multiselect-blurred {
            background: lightgray;
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
                <h5 class="card-title">Send WhatsApp Notifications</h5>

                <div class="error" id="err"></div>




                {!! Form::open([
                    'id' => 'sendnotiform',

                    'method' => 'POST',
                    'class' => 'row g-3',
                    'enctype' => 'multipart/form-data',
                ]) !!}
                <input type="hidden" name="from" value="admin">
                {{-- Just for Tracking Purpose --}}
                <input type="hidden" name="user_question" value="admin-side-booking">

                <div class="row mt-3">

                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroupPrepend2">Pick Date</span>
                            <input type="date" class="form-control" name="pick_venue_date" id="pick_venue_date">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroupPrepend2">Pick Dua Type</span>
                            <select class="form-control" name="dua_type" id="type_dua">
                                <option value=""> -- Select Dua Option-- </option>
                                <option value="dum">Dum</option>
                                <option value="dua">Dua</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info float-end" type="button" id="getList"> Get List </button>
                    </div>

                </div>

                <div class="row mt-5">

                    <div class="col-md-5">

                        <div class="multiselect form-control" id="userMobile" style="height: 300px;overflow:auto">
                            <label> Please Select Date and Dua Type</label>

                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="input-group">
                            <textarea name="whatsAppMessage" class="form-control" cols="40" rows="12" placeholder="Write message here"></textarea>

                        </div>
                    </div>

                </div>










                <div class="col-12">
                    <button class="btn btn-primary" type="button" id="sendNotification">Send Notification</button>
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

        $("#getList").click(function() {
            var date = $("#pick_venue_date").val();
            var typeDua = $("#type_dua").val();
            $(this).text('Searching ...')
            $.ajax({
                url: "{{ route('get-visitor') }}",
                type: 'POST',
                data: {
                    dua_option: typeDua,
                    venueDate: date,
                    _token: "{{ csrf_token() }}"
                },

                success: function(response) {
                    var options = '';

                    if (response.success) {
                        $("#err").empty()
                        $.each(response.data, function(i, item) {
                            options +=
                                `<label><span></span><input type="checkbox" name="user_mobile[]" value="${item.country_code}${item.phone}">  ${item.phone}  (${item.dua_type})</label>`;
                        })
                        $("#userMobile").html('<label><span></span><input type="checkbox" name="check_all" id="checkAll"> Check All</lable>'+options)
                        $("#getList").text('Get List')
                    } else {
                        $("#err").empty()
                        $("#getList").text('Get List')
                        $("#userMobile").html('No user for this input')
                    }



                    // You can proceed with form submission here
                },
                error: function(xhr) {
                    var err = '';

                    $.each(xhr.responseJSON.errors, function(i, item) {
                        err += `<p class="alert alert-danger" >${item}</p>`;
                    });
                    $("#err").html(err)
                    $("#getList").text('Get List')
                }
            });
        })

        $("#sendNotification").click(function() {

            var formData = new FormData($("#sendnotiform")[0]);
            $(this).text('Sending ...')

            $.ajax({
                url: "{{ route('whatsapp.notication.show') }}", // Assuming the form action is set
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $("#err").empty()

                    alert("Message Send")
                    $("#sendNotification").text('Send Notification')
                    location.reload();
                    // Handle success response after form submission
                },
                error: function(xhr) {
                    var err = '';

                    $.each(xhr.responseJSON.errors, function(i, item) {
                        err += `<p class="alert alert-danger" >${item}</p>`;
                    });
                    $("#err").html(err)
                    $("#sendNotification").text('Send Notification')

                }
            });

        });

        $(document).on("click", "#checkAll", function() {
            var isChecked = $(this).prop("checked");
            $("#userMobile input[type='checkbox']").prop("checked", function(_, oldProp) {
                $(this).prop("checked",false)
                return !oldProp;
            });
            $("#userMobile input[type='checkbox']").prop("checked", isChecked);
        });

    </script>
@endsection
