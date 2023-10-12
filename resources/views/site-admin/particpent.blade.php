@extends('layouts.app')

@section('content')
    <style>
        @import url("https://fonts.googleapis.com/css?family=Montserrat:400,600,700");

        * {
            box-sizing: border-box;
        }

        .friend-list {
            font-family: "Montserrat", sans-serif;
        }

        .friend-list .friend-box {
            position: relative;
            display: inline-block;
            width: 500px;
            height: 140px;
            background-color: #eee;
            margin: 20px;
            border-radius: 10px;
        }

        .friend-list .friend-profile {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -20px;
            border-radius: 50%;
            height: 70px;
            width: 70px;
            background-size: cover;
            background-position: center;
            border: 3px rgba(255, 255, 255, 0.7) solid;
            box-shadow: 0px 0px 15px #aaa;
        }

        .friend-list .name-box {
            text-align: center;
            position: absolute;
            top: 55px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            color: #4F7091;
            font-size: 18px;
        }

        .friend-list .user-name-box {
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            text-align: center;
            font-size: 12px;
        }

        .friend-list .level-indicator {
            background-color: #00a5db;
            color: #fff;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 10px;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
        }

        .friend-requests {
            font-family: "Montserrat", sans-serif;
            text-align: center;
        }

        .friend-requests .friend-box {
            position: relative;
            display: inline-block;
            width: 300px;
            height: 140px;
            background-color: #eee;
            margin: 20px;
            border-radius: 10px;
        }

        .friend-requests .friend-profile {
            position: absolute;
            left: 10px;
            top: 10px;
            border-radius: 50%;
            height: 70px;
            width: 70px;
            background-size: cover;
            background-position: center;
            border: 3px rgba(255, 255, 255, 0.7) solid;
            box-shadow: 0px 0px 15px #aaa;
        }

        .friend-requests .name-box {
            text-align: left;
            position: absolute;
            top: 20px;
            left: 90px;
            width: 200px;
            color: #4F7091;
            font-size: 18px;
        }

        .friend-requests .user-name-box {
            position: absolute;
            top: 50px;
            left: 90px;
            width: 200px;
            text-align: left;
            font-size: 12px;
            line-height: 16px;
        }

        .friend-requests .request-btn-row {
            position: absolute;
            left: 10px;
            width: calc(100% - 20px);
            bottom: 10px;
            text-align: center;
        }

        .friend-requests .request-btn-row .friend-request {
            width: 35%;
            margin: 5px 5%;
            border-radius: 5px;
            border: 2px solid transparent;
            padding: 5px;
            cursor: pointer;
        }

        .friend-requests .request-btn-row .decline-request {
            background-color: #FF6666;
            color: #fff;
        }

        .friend-requests .request-btn-row .decline-request:hover {
            background-color: #993333;
        }

        .friend-requests .request-btn-row .accept-request {
            background-color: #41c764;
            color: #fff;
        }

        .friend-requests .request-btn-row .accept-request:hover {
            background-color: #419764;
        }

        .friend-requests .request-btn-row .fr-request-pending {
            position: relative;
            top: -10px;
            color: #17406f;
            font-weight: bold;
        }

        .friend-requests .request-btn-row.disappear {
            display: none;
        }

        .friend-requests {
            border: 3px dotted black;
            padding: 100px;
        }


        @media (max-width:767px) {
            .friend-requests {
                border: 3px dotted black;
                padding: 0 !important;
            }

            .friend-list {
                text-align: center !important;
            }
            .friend-list .friend-box {
            position: relative;
            display: inline-block;
            width: 310px;
            height: 140px;
            background-color: #eee;
            margin: 20px;
            border-radius: 10px;
        }
        }

        @media (min-width:1024px) {

            .friend-requests {
                border: 3px solid black;
                padding: 0px !important;
            }
        }
        .friend-list {
    text-align: center;
}
.level-success {
    background: green !important;
}
.level-danger {
    background: red !important;
}
    </style>
    <div class="friend-list">
        @if (!empty($venueAddress))
        <div class="friend-box">
            @if (!empty($venueAddress->thripist->profile_pic) && Storage::disk('s3_general')->exists('images/' .$venueAddress->thripist->profile_pic))
            <div class="friend-profile" style="background-image: url({{ env('AWS_GENERAL_PATH') . 'images/' . $venueAddress->thripist->profile_pic}});"></div>
                @else
                <div class="friend-profile" style="background-image: url(https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg);"></div>
            @endif
            <div class="friend-profile"
                style="background-image: url({{ env('AWS_GENERAL_PATH') . 'images/' . $venueAddress->thripist->profile_pic}});"></div>
            <div class="name-box">{{ ($venueAddress) ? $venueAddress->thripist->name: "" }}</div>
            <div class="user-name-box d-none">{{ $venueAddress->thripist->name }}</div>
            @if(!empty( $venueAddress->thripist->status ))
               <div class="level-indicator mt-2">{{  $venueAddress->thripist->status }}</div>
            @else
               <div id="user-status" class="level-indicator level-success mt-2 user-status">Online</div>
            @endif
           
        </div>
        @endif
    </div>
    <div class="friend-requests" id="particpents">
        <div class="friend-box No-request"> <div class="name-box"> No Request Yet Received</div> </div>
    </div>
@endsection


@section('page-script')
    <script>
        document.title = "KahayFaqeer.com| Participant";
    </script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var id = "{{ $id }}";
        var audioToneUrl = '';
        const audioElement = new Audio('https://kahayfaqeer-general-bucket.s3.amazonaws.com/notification1.wav');


        $(document).ready(function() {

            Pusher.logToConsole = false;

            var pusher = new Pusher('0d51a97603f510fb700e', {
                cluster: 'ap2'
            });
            var channel = pusher.subscribe('site-admin-' + id);
            pusher.connection.bind('connected', function() {
                console.log('Pusher connected');
            });
            channel.bind('user.notification', function(data) {

                var response = JSON.stringify(data);
                var resp = JSON.parse(response)
                if (resp.message == 'online') {
                    $("#user-status").text(resp.message)
                    $("#user-status").removeClass('level-danger').addClass('level-success');
                } else {
                    $("#user-status").text(resp.message)
                    $("#user-status").removeClass('level-success').addClass('level-danger');
                     
                }


            });

        })
    </script>

    <script>
        var imagePath = "{{ env('AWS_GENERAL_PATH') . 'images/' }}";
        var defaultPath = "{{ asset('assets/theme/img/avatar.png') }}";

        $(document).ready(function() {
            fetchParticipants();
            setInterval(() => {
                fetchParticipants();
            }, 10000);

            function fetchParticipants() {
                $.ajax({
                    type: "post",
                    url: "{{ route('visitor.list') }}", // Replace with the actual route
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.participants) {
                            updateParticipantsList(response.participants);
                        }
                    },
                    error: function() {
                        console.error("An error occurred while fetching the participant list.");
                    },
                });
            }

            function updateParticipantsList(participants) {

                var html = '';
                var userinfo = participants.user_info;
                var status = '';
                if(userinfo.status == null || userinfo.status == ''){
                    status = 'online'
                    $("#user-status").text(status)
                    $("#user-status").removeClass('level-danger').addClass('level-success');
                }else{
                    status = userinfo.status
                    $("#user-status").text(status)
                    $("#user-status").removeClass('level-success').addClass('level-danger');

                }

               
 
                $("#name").text(userinfo.name)
                delete participants.user_info;
                if (participants) {
                    $.each(participants, function(key, item) {

                        var userStatus = '';
                        if (item.user_status == 'in-queue') {
                            userStatus = 'Waiting';
                        }
                        html+=`<div class="friend-box">
                                <div class="friend-profile" style="background-image: url(https://img.icons8.com/ios/50/user--v1.png);"></div>
                                <div class="name-box">${item.fname} ${item.lname}</div>
                                <div class="user-name-box">@${item.fname} sent you a request.</div>
                                <div class="request-btn-row" data-username="sadrabbit534">
                                    <button class="friend-request accept-request admit-button"
                                    data-id="${item.id}">Accept</button>
                                    <button class="friend-request decline-request dismiss-button"
                                    data-id="${item.id}">Decline</button>
                                </div>
                        </div>`;
                        
                    })
                } else {
                    html = '<div class="friend-box"><div class="friend-box No-request"> <div class="name-box"> No Request Yet Received</div> </div></div>';
                }
                $("#particpents").html(html)

            }
            // Function to fetch the list of participants
            function AdmitRequest(participantId, action) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('asktojoin') }}", // Replace with the actual route
                    data: {
                        id: participantId,
                        action: action,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response from the server

                        // Request successful
                        // alert("Request to join sent successfully.");

                    },
                    error: function() {
                        // Request failed
                        alert("An error occurred while sending the request.");
                    },
                });
            }

            $(document).on("click", ".admit-button", function() {
                var participantId = $(this).data("id");
                setTimeout(() => {
                    $(this).parents('.friend-box').fadeOut();
                }, 2000);
                AdmitRequest(participantId, 'admitted');

            });
            $(document).on("click", ".dismiss-button", function() {
                var participantId = $(this).data("id");
                AdmitRequest(participantId, 'dismissed');
            });


        });
    </script>
@endsection
