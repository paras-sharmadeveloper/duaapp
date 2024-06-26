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
                <form method="POST" class="row g-3" id="sendnotiform">

                    @csrf

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
                                    <option value="working_lady_dua">Working Dua</option>
                                    <option value="working_lady_dum">Working Dum</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-info float-end" type="button" id="getList"> Get List </button>
                        </div>

                    </div>

                    <div class="row mt-5">

                        <div class="col-md-5">
                            <label for="reason_english" class="form-label"> User Mobile List </label>
                            <input class="form-control mb-3" id="searchInput" type="text" placeholder="search here">

                            <div class="multiselect form-control" id="userMobile" style="height: 300px;overflow:auto">
                                <label> Please Select Date and Dua Type</label>

                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input token_template" id="token_template"
                                    name="token_template">
                                <label class="form-check-label" for="check1">Load Token Template</label>
                            </div>
                            <label for="reason_english" class="form-label">WhatsApp Message

                            </label>
                            <div class="input-group">

                                <textarea name="whatsAppMessage" id="whatsAppMessage" class="form-control" cols="40" rows="12"
                                    placeholder="Write message here"></textarea>

                            </div>
                        </div>

                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="button" id="sendNotification">Send Notification</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <table class="table-with-buttons table table-responsive cell-border mt-5">
                    <thead>
                        <tr>
                            <th>Venue Date</th>
                            <th>Dua Type</th>
                            <th>Message</th>
                            <th>Mobile</th>
                            <th>Message Sid</th>
                            <th>Message Sent Status</th>
                            <th>Message Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->venue_date }}</td>
                                <td>{{ $log->dua_type }}</td>
                                <td>{{ $log->whatsAppMessage }}</td>
                                <td>{{ $log->mobile }}</td>
                                <td>{{ $log->msg_sid }}</td>
                                <td>{{ $log->msg_sent_status }}</td>
                                <td>{{ $log->msg_date }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

        $(".token_template").change(function() {
            var isChecked = $(this).prop("checked");
            console.log("token_template", isChecked)
            var nt = '';
            if (isChecked) {
                nt = `Asalamualaikum,
Please see below confirmation for your dua token.

Your Dua Ghar : {city}
Your Dua Date : {date}
Your Online Dua Token : {token_url}
Your Token Number : {token_number}
Your Dua Type : {dua_type}
Your registered mobile: {mobile}

Please reach by 1pm to validate and print your token.

Read and listen all books for free. Please visit KahayFaqeer.org`;

            }


            $("#whatsAppMessage").val(nt)
        })

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
                                `<label><span></span><input type="checkbox" name="user_mobile[${item.id}]" value="${item.country_code}${item.phone}">  ${item.phone}  (${item.dua_type})</label>`;
                        })
                        $("#userMobile").html(
                            '<label><span></span><input type="checkbox" name="check_all" id="checkAll"> Check All</lable>' +
                            options)
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

                    // alert("Message Send")
                    $("#sendNotification").text('Send Notification')
                    alert("Message Sent");
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
                $(this).prop("checked", false)
                return !oldProp;
            });
            $("#userMobile input[type='checkbox']").prop("checked", isChecked);
        });





        function searchInMultiselect() {

            // Get the input value from the search box
            var searchText = document.getElementById('searchInput').value.toLowerCase();
            if(searchText == ''){
                $("#checkAll").show();
            }else{
                $("#checkAll").hide();
            }

            // Get all labels inside the multiselect div
            var labels = document.querySelectorAll('.multiselect label');

            // Loop through each label to find the matching text
            labels.forEach(function(label) {
                var text = label.textContent.toLowerCase(); // Get the text content of the label

                // Check if the text contains the search text
                if (text.includes(searchText)) {
                    label.style.display = 'block';

                } else {
                    label.style.display = 'none';

                }
            });
        }
        var searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', searchInMultiselect);
    </script>
@endsection
