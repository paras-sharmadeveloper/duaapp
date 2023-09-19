@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('roles.index') }}"> <i
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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Manage Queue</h5>
           

            @if (request()->route()->getName() == 'siteadmin.queue.list')
                <table class="table table-bordered datatableasd table-striped ">
                    <thead>
                        <tr>
                            <th scope="col">BookingId</th>
                            <th scope="col">UserName</th>
                            <th scope="col">BookingTime</th>
                            <th scope="col">Confirmed</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">


                    </tbody>
                </table>
            @endif
        </div>
    </div>
    </div>

@endsection
@section('page-script') 
    <script>
        getData(); 
        setInterval(function () {getData(); }, 25000);
        var url = "{{ route('siteadmin.queue.list',[request()->route('id')]) }}"
        function getData() {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    var html = '';
                    $.each(response.data, function(key, slot) {
                        $.each(slot.visitors, function(k, visitor) {
 
                            if (visitor.is_available === 'confirmed') {
                                var confirmedAt = new Date(visitor.confirmed_at);
                                var formattedDate = formatDateTime(confirmedAt); 
                                var badgeHtml = '<span class="badge bg-success"> Confirmed (' +
                                    formattedDate + ')</span>';
                            } else {
                                var badgeHtml =
                                    '<span class="badge bg-danger"> Not Confirmed </span>';
                            }

                            html += `<tr>
                                <th scope="row">${visitor.booking_number }</th>
                                <td>${visitor.fname}  ${visitor.lname}</td>
                                <td>${convertTo12HourFormat(slot.slot_time)}</td>
                                <td> ${badgeHtml}</td>
                                <td>
                                    <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                                    <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                </td>
                                </tr>`;
                           });

                    });
                    $("#tbody").html(html) 
                },
                error: function(xhr, status, error) { 
                    console.error(error);
                }
            });

        }

        function formatDateTime(dateTimeString) {
            const options = {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
            };

            const formattedDate = new Date(dateTimeString).toLocaleString('en-US', options);
            return formattedDate;
        }


        function convertTo12HourFormat(time24) {
            const [hour, minute, second] = time24.split(':');
            let period = 'AM';

            // Convert to 12-hour format and set the period (AM or PM)
            let hour12 = parseInt(hour, 10);
            if (hour12 >= 12) {
                period = 'PM';
                if (hour12 > 12) {
                    hour12 -= 12;
                }
            }

            // Format with leading zeros (e.g., "03:15 PM")
            // const time12 = `${hour12.toString().padStart(2, '0')}:${minute}:${second} ${period}`;
            const time12 = `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;
            return time12;
        }
    </script> 
@endsection
