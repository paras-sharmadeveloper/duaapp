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
        setInterval(function () {getData(); }, 15000);
        var url = "{{ route('siteadmin.queue.list',[request()->route('id')]) }}";
        function getData() {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    var html = '';
                    $.each(response.data, function(key, slot) {
                        $.each(slot.visitors, function(k, visitor) {
                            var timeinSec=0; 
                            var none='';
                            var nonetimer='';
                            if (visitor.is_available === 'confirmed') {
                               
                                var confirmedAt = new Date(visitor.confirmed_at);
                                var formattedDate = formatDateTime(confirmedAt); 
                                var badgeHtml = '<span class="badge bg-success"> Confirmed (' +
                                    formattedDate + ')</span>';
                            } else {
                                none ='d-none'; 
                                nonetimer ='d-none'; 
                                var badgeHtml =
                                    '<span class="badge bg-danger"> Not Confirmed </span>';
                            }
                            
                            if (visitor.meeting_start_at != null && visitor.meeting_ends_at != null) {
                                none = 'd-none'; 
                                timeinSec =  timeDiff(visitor.meeting_start_at,visitor.meeting_ends_at); 
                            }else{
                                nonetimer ='d-none'; 
                            } 
                            html += `<tr>
                                <th scope="row">${visitor.booking_number }</th>
                                <td>${visitor.fname}  ${visitor.lname}</td>
                                <td>${convertTo12HourFormat(slot.slot_time)}</td>
                                <td> ${badgeHtml}</td>
                                <td class="action-td">
                                    
                                    <button type="button" class="btn btn-success start ${none}" data-id="${visitor.id}"><div id="timer${visitor.id}">Start</div></button>
                                    <button type="button" class="btn btn-danger stop ${none}" data-id="${visitor.id}">Stop</button>
                                    <button type="button" class="btn btn-info hold ${none}">Hold</button>
                                    <span class="badge bg-info ${nonetimer}"> total time : ${timeinSec} Sec </span>
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
        var timerInterval; 
        $(document).on("click",".start",function(){
            var id = $(this).attr('data-id'); 

            var startTime = new Date().getTime();
            var endTime = startTime + 60000; // 1 minute in milliseconds
            postAjax(id,'start'); 
            $(this).prop("disabled",true);
            // Update the timer every second
            timerInterval = setInterval(function() {
                var currentTime = new Date().getTime();
                var remainingTime = endTime - currentTime;

                if (remainingTime <= 0) {
                    clearInterval(timerInterval);
                    $("#timer"+id).text("Time's up!");
                   //  
                } else { 
                    
                    var seconds = Math.floor(remainingTime / 1000) % 60;
                    var minutes = Math.floor(remainingTime / 1000 / 60);
                    $("#timer"+id).text(seconds + "s");
                    // $("#timer").text(minutes + "m " + seconds + "s");
                }
            }, 1000);
        })
        $(document).on("click",".stop",function(){
            clearInterval(timerInterval);
            var id = $(this).attr('data-id'); 
            $(this).parents(".action-td").find(".start").prop("disabled",true); 
            $(this).prop("disabled",true); 
            $(this).parents(".action-td").find(".hold").prop("disabled",true); 
            // $("#timer"+id).;
            postAjax(id,'end');
        });
        $(document).on("click",".hold",function(){
            clearInterval(timerInterval);
            var id = $(this).attr('data-id'); 
            $(this).parents(".action-td").find(".start").prop("disabled",true); 
            $(this).text("Resume"); 
            $(this).removeClass("hode").addClass('resume');
            $(this).parents(".action-td").find(".stop").prop("disabled",true); 
            // $("#timer"+id).;
            // postAjax(id,'end');
        });

        $(document).on("click",".resume",function(){
            clearInterval(timerInterval);
            var id = $(this).attr('data-id'); 
            $(this).parents(".action-td").find(".start").prop("disabled",false); 
            $(this).text("Hold"); 
            $(this).removeClass("resume").addClass('hold');
            $(this).parents(".action-td").find(".stop").prop("disabled",false); 
            // $("#timer"+id).;
            // postAjax(id,'end');
        });

        function postAjax(id,type){

            var url = "{{ route('siteadmin.queue.vistor.update', ['id' => ':id']) }}";
            url = url.replace(':id', id);
           
            $.ajax({ 
                url: url,
                type: "POST",
                data:{
                    type:type,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    console.log(response); 
                },
                error: function(xhr, status, error) { 
                    console.error(error);
                }
            });

        }

        function timeDiff(startTimeStr,endTimeStr){
            var startTime = new Date(startTimeStr);
            var endTime = new Date(endTimeStr);
            var timeDifference = endTime - startTime;
            var hours = Math.floor(timeDifference / (1000 * 60 * 60));
            var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);
            return seconds; 
        }

       
    </script> 
@endsection
