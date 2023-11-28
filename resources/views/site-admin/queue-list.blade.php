@extends('layouts.app')
@section('content')
<style>
    .main-data {
    max-height: 100vh;
    overflow: auto;
}
</style>
<input type="hidden" name="last-running-id" id="last-running-id" value="0">
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
    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> 
    @endif 
    <div class="card">
        <div class="card-body">
            <div class="action d-flex justify-content-between">
                <h5 class="card-title">Manage Users (Refeshed in 5 sec automatically )</h5>
                <div class="row">
                    <div class="col-xl-12">
                        <input type="text" name="" id="search" class="form-control" placeholder="search">
                    </div>
                </div>
            </div>


            @if (request()->route()->getName() == 'siteadmin.queue.list')



            <div class="row main-data">
 
                <div class="col-xl-12  users-list-header">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="tokn">
                                    <span class="fw-bold rounded-circle text-center">Sr.No</span>
                                </div>
                                <div class="ms-3">
                                    <p class="fw-bold mb-1">User Info</p>
                                </div> 
                                <span class="fw-bold">Action</span>
                            </div>
                        </div>

                    </div>
                </div>


                @php $i=0;     @endphp
                 <div class="users-list-main" id="users-list-main">
                    @foreach ($venueSloting as $visitoddr)
                @foreach ($visitoddr->visitors as $visitor)
               
               
                <div class="col-xl-12 mb-1 users-list">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="token">
                                    <span class="rounded-circle text-center h6">{{ ++$i }}</span>
                                </div>
                                <div class="ms-3">
                                     
                                    <p class="fw-bold mb-1 h6"> 
                                        {{ $visitor['fname'] }} {{ $visitor['lname'] }}
                                        <h6 class="sub-title"> Mobile : {{ $visitor['phone'] }}</h6>
                                        <h6 class="sub-title"> Email : {{ $visitor['email'] }}</h6>
                                        <h6 class="sub-title"> TokenNo : {{ $visitor['booking_number'] }}</h6> 
                                    </p>
                                    
                                        
                                   
                                </div>
                                <div class="info text-end">
                                   
                                        @if(empty($visitor->confirmed_at))
                                        <button type="button" class="btn btn-info text-white bg-color-info verify"
                                            data-loading="Verifying..." data-success="Verified" data-default="Verify" data-id="{{ $visitor->id }}" >
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                                style="display:none">
                                            </span>
                                            <b>Verify User </b>
                                        </button> 
                                        @endif
                                   
                                   
                                    
                                    <button type="button"  
                                    @if(!empty($visitor->confirmed_at))
                                    class="btn btn-success start mb-2 start{{$visitor->id  }}"
                                    @else
                                    class="btn btn-success start mb-2 d-none start{{$visitor->id  }}"
                                    @endif
                                    
                                     
                                        data-minutes="{{ $visitoddr->venueAddress->slot_duration }}" 
                                        data-id="{{ $visitor->id }}">
                                        <div id="timer{{ $visitor->id }}">Start</div>
                                    </button>
                                </div> 
                                 
                                {{-- <span class="badge rounded-pill badge-success h2">Active</span> --}}
                                {{-- <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span> --}}
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
                @endforeach

                 </div>
                
                 
             </div> 



















                <table class="table-with-buttons-no table table-responsive cell-border d-none">
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
        setInterval(function() {
            getData();
        }, 5000);
        var url = "{{ route('siteadmin.queue.list', [request()->route('id')]) }}";

        function getData() {
            var lastid = $("#last-running-id").val();

            
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    var html = '';
                    var i = 1; 
                    $.each(response.data, function(key, slot) {

                        $.each(slot.visitors, function(k, visitor) {
                            var timeinSec = 0;
                            var none = '';
                            var nonetimer = '';
                            var isConfirmed = false; 
                            var confirmedHtml='' , badgeHtml = ''; 
                            if (visitor.is_available === 'confirmed' || visitor.confirmed_at!==null ) {
                                isConfirmed = true; 
                                var confirmedAt = new Date(visitor.confirmed_at);
                                var formattedDate = formatDateTime(confirmedAt);
                                 confirmedHtml = '<span class="badge bg-success">'+visitor.user_status+'</span>';
                            } else {
                                none = 'd-none';
                                nonetimer = 'd-none';
                                 badgeHtml = `<button   type="button" 
                                                        class="btn btn-info text-white bg-color-info verify" 
                                                        data-loading="Verifying..." 
                                                        data-success="Verified" 
                                                        data-default="Verify" 
                                                        data-id="${visitor.id}"
                                                    >
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                                    </span>
                                                    <b>Verify User </b>
                                        </button>`;
                            }

                            if (visitor.meeting_start_at != null && visitor.meeting_ends_at !=
                                null) {
                                none = 'd-none';
                                var timeDifference = timeDiff(visitor.meeting_start_at, visitor.meeting_ends_at, slot.venue_address.slot_duration);

                                var hours = timeDifference.hours;
                                var minutes = timeDifference.minutes;
                                var seconds = timeDifference.seconds;
                                // timeinSec = timeDiff(visitor.meeting_start_at, visitor.meeting_ends_at, slot.venue_address.slot_duration);
                            } else {
                                nonetimer = 'd-none';
                            }
                            var btnText = 'Start';
                            var btnprop = '';
                            if(visitor.meeting_start_at != null){
                                btnText = 'Started';
                                btnprop ='disabled'; 
                                $("#last-running-id").val(visitor.id);
                            }
                            // meeting_start_at
                            html +=`<div class="col-xl-12 mb-1 users-list">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">

                                            <div class="token">
                                                <span class="rounded-circle text-center h6">${i}</span>
                                            </div>
                                            <div class="ms-3">
                                                
                                                <p class="fw-bold mb-1 h6"> 
                                                    ${visitor.fname} ${visitor.lname}
                                                    </p><h6 class="sub-title"> Mobile : ${visitor.phone}</h6>
                                                    <h6 class="sub-title"> Email : ${visitor.email}</h6>
                                                    <h6 class="sub-title"> TokenNo : ${visitor.booking_number} </h6> 
                                                    <h6 class="vert">${confirmedHtml}</h6>
                                                    <span class="badge bg-info ${nonetimer}"> total time : ${minutes} minutes ${seconds} Sec </span>
                                                <p></p> 
                                            </div>
                                            <div class="info text-end">
                                                 
                                                    ${badgeHtml}
                                                
                                                                                    
                                                <button type="button" class="btn btn-success start mb-2 start${visitor.id} ${none}" 
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" ${btnprop}>
                                                    <div id="timer${visitor.id}">${btnText}</div>
                                                </button>

                                                <button type="button" class="btn btn-danger stop mb-2 stop${visitor.id} ${none}" 
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" >
                                                    <div id="timer${visitor.id}">Stop</div>
                                                </button>
                                                
                                            </div>  
                                        </div>
                                    </div>

                                </div>
                            </div>`; 
                            // html += `<tr>
                            //     <th scope="row">${visitor.booking_number }</th>
                            //     <td>${visitor.fname}  ${visitor.lname}</td>
                            //     <td>${convertTo12HourFormat(slot.slot_time)}</td>
                            //     <td> ${badgeHtml}</td>
                            //     <td class="action-td">
                                    
                            //         <button type="button" class="btn btn-success start ${none}" data-minutes="${slot.venue_address.slot_duration}" data-id="${visitor.id}"><div id="timer${visitor.id}">Start</div></button>
                            //         <button type="button" class="btn btn-danger stop ${none}" data-id="${visitor.id}">Stop</button>
                            //         <button type="button" class="btn btn-info hold d-none ${none}">Hold</button>
                            //         <span class="badge bg-info ${nonetimer}"> total time : ${minutes} minutes ${seconds} Sec </span>
                            //     </td>
                            //     </tr>`;
                                i++; 
                        });

                    });
                    // $("#tbody").html(html)
                    $("#users-list-main").html(html)
                    
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
        $(document).on("click", ".start", function() {
            $this = $(this);
            var id = $this.attr('data-id');
            var lastid = $("#last-running-id").val();
 
            var duration = parseInt($this.attr('data-minutes')); // Parse the value as an integer

            var startTime = new Date().getTime();
            var totalTime = duration * 60000;
            var timeInterval = duration * 1000;
            var endTime = startTime + totalTime;
            $("#last-running-id").val(id);
           
            postAjax(id, 'start' , $this);
            if(lastid > 0){
                postAjax(lastid, 'end' ,  $this);     
            }
            
            // $(this).prop("disabled", true);

            // Update the timer every second
            var timerInterval;

            timerInterval = setInterval(function() {
                var currentTime = new Date().getTime();
                var remainingTime = endTime - currentTime;

                if (remainingTime <= 0) {
                    clearInterval(timerInterval);
                    $("#timer" + id).text("Time's up!");
                    
                  
                } else {
                    var seconds = Math.floor(remainingTime / 1000) % 60;
                    var minutes = Math.floor(remainingTime / 1000 / 60);
                    $("#timer" + id).text(minutes + "m " + seconds + "s");
                }
            }, 1000);
        });
        $(document).on("click", ".stop", function() {
            $this = $(this);
            clearInterval(timerInterval);
            var id = $(this).attr('data-id');
            $(this).parents(".action-td").find(".start").prop("disabled", true);
            $(this).prop("disabled", true);
            $(this).parents(".action-td").find(".hold").prop("disabled", true);
            // $("#timer"+id).;
            postAjax(id, 'end' , $this);
        });
        $(document).on("click", ".verify", function() {
            $this = $(this);
            var id = $(this).attr('data-id'); 
             postAjax(id, 'verify' , $this);
        });

        

        

        function postAjax(id, type,event) {
              
            var loadingText = event.attr('data-loading');
            var successText = event.attr('data-success');
            var defaultText = event.attr('data-default');
            var url = "{{ route('siteadmin.queue.vistor.update', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            event.find('span').show()
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    type: type,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    event.find('b').text(successText)
                    setTimeout(() => {
                        event.find('b').text(defaultText) 
                    }, 1500);
                    event.find('span').hide()
                    $(".start"+id).removeClass('d-none')
                    event.fadeOut(); 
                    $(".vert").html('<span class="badge bg-success">Confirmed</span>')

                    console.log(response);
                },
                error: function(xhr, status, error) {
                    event.find('span').hide()
                    $(".start"+id).addClass('d-none')
                    event.find('b').text(defaultText)
                    console.error(error);
                }
            });

        }

        function timeDiff(startTimeStr, endTimeStr, duration) {
            var seconds = duration * 1000;
            var startTime = new Date(startTimeStr);
            var endTime = new Date(endTimeStr);
            var timeDifference = endTime - startTime;
            
            var hours = Math.floor(timeDifference / (seconds * 60 * 60));
            var minutes = Math.floor((timeDifference % (seconds * 60 * 60)) / (seconds * 60));
            var remainingSeconds = Math.floor((timeDifference % (seconds * 60)) / seconds);
            
            return {
                hours: hours,
                minutes: minutes,
                seconds: remainingSeconds
            };
        }

        $(document).ready(function() {
        // Add an input event listener to the search input
        $("#search").on("input", function() {
            clearInterval(timerInterval);
            // Get the search input value
            var searchText = $(this).val().toLowerCase();

            // Loop through each card
            $(".users-list .card").each(function() {
                // Get the text content of each card
                var cardText = $(this).text().toLowerCase();

                // Check if the card text contains the search text
                if (cardText.includes(searchText)) {
                    // Show the card if it matches
                    $(this).show();
                } else {
                    // Hide the card if it doesn't match
                    $(this).hide();
                }
            });
        });
    });
    </script>
@endsection
