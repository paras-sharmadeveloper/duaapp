@extends('layouts.guest')
@section('content')
    <style>
        .a {
            width: 55%;
        }

        .b {
            width: 40%;
        }

        .b .card {
            height: 100vh;
        }

        .token span {
            /* border: 1px solid; */
            padding: 10px;
            /* font-size: 28px; */
            font-weight: bold;
        }

        .users-list-header .card {
            background-color: #00BCD4;
            color: #fff;
        }

        .curnt-token-runing {
            font-size: 11rem;
        }

        .users-list span {
            font-size: 1.6rem;
        }
    </style>
    <div class="container-fluid d-flex justify-content-around mt-4">
        <div class="a">
            <div class="row">


                <div class="col-xl-12 mb-4 users-list-header">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="tokn">
                                    <span class="fw-bold rounded-circle text-center">Token</span>
                                </div>
                                <div class="ms-3">
                                    <p class="fw-bold mb-1">UserInfo</p>
                                </div>
                                <span class="fw-bold">Status</span>
                                <span class="fw-bold">Booked Time </span>
                                <span class="fw-bold">Estimated Time</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div id="current-user-listing">
                 
               </div>

            </div>
        </div>
        <div class="b">
            <div class="row">
                <div class="col-xl-12 mb-4 current-token">
                    <div class="card">
                        <div class="card-body text-center">
                            <h2 class="card-title text-center">Active Token</h5>
                                <p class="curnt-token-time" id="active-time"> 00:00:00 </p>
                                <span class="curnt-token-runing badge badge-success" id="active-token"> 00 </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script>
        var url = "{{ route('waiting-queue', request()->id) }}";
        getList();
        setInterval(() => {
            getList(); 
        }, 2500);

       
       

        function getList() {
            var html = '';
            $.ajax({
                url: url, // Update the URL to your Laravel endpoint
                method: 'GET',

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    $.each(response.data, function(i, item) {
                        var className,textName,tokenNumber,meeting_start_at=''; 
                        if (item.user_status === 'no_action' || item.user_status === 'in-queue') {
                            className = 'badge rounded-pill badge-warning h2';
                            textName = 'Awating..'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'admitted') {
                            className = 'badge rounded-pill badge-info h2';
                            textName = 'confirmed'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'meeting-end') {
                            className = 'badge rounded-pill badge-info h2';
                            textName = 'Meeting End'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'in-meeting') {
                            className = 'badge rounded-pill badge-success h2';
                            textName = 'In Meeting'; 
                            meeting_start_at = item.meeting_start_at; 
                            $("#active-token").text(item.booking_number)
                            $("#active-time").text(formatTime(item.meeting_start_at))
                        }
                        
                        html += `<div class="col-xl-12 mb-4 users-list">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="token">
                                            <span class="rounded-circle text-center h2">${item.token_id}</span>
                                        </div>
                                        <div class="ms-3">
                                            <p class="fw-bold mb-1 h2">${item.fname} ${item.lname} d</p>
                                            <p class="text-muted mb-0 h6">${item.email}</p>
                                            <p class="text-muted mb-0 h6">${item.phone}</p>
                                        </div>

                                        <span class="${className}">${textName}</span>
                                        <span class="badge badge-warning rounded-pill d-inline h1" id="estimated-time-2">${formatTime(item.venue_date+ ' ' +item.slot_time)} </span>
                                        <span class="badge badge-warning rounded-pill d-inline h2">${item.slot_duration} Minute</span> 
                                    </div>
                                </div>

                            </div>
                        </div>`;
                        console.log("item", item.slot_time)
                    })
                    $("#current-user-listing").html(html)



                    console.log("response", response)
                },
                error: function(error) {


                }
            });

        }

        function formatTime(timeValue) {
            console.log(timeValue)
            // Create a Date object by combining the date part with the time value
            var date = new Date(timeValue);

            // Format the time using options
            var formattedTime = date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            return formattedTime;
        }
    </script>
@endsection
