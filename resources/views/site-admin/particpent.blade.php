@extends('layouts.app')

@section('content')
    <style>
        .avatar {
            overflow: hidden;
        }

        .user-icon {
            font-size: 2em;
            /* change font size should change size of icon */
            float: left;
            margin: 1em;
            /* remove this line to avoid float/margin */
        }

        .user-icon {
            border-radius: 4em;
            border: 1px solid skyblue;
            height: 6em;
            width: 6em;
            background: none;
            padding: 0.1em;
        }

        .user-icon::before {
            content: " ";
            display: block;
            height: 2em;
            width: 2em;
            background: skyblue;
            position: relative;
            left: 2em;
            top: 0.8em;
            border-radius: 2em;
        }

        .user-icon::after {
            content: " ";
            display: block;
            height: 2em;
            width: 4em;
            background: skyblue;
            position: relative;
            left: 1em;
            top: 1em;
            border-radius: 2em 2em 0 0;
        }

        .red,
        .amber,
        .green {
            display: block;
            height: 0em;
            width: 0em;
            background: none;
            position: relative;
            left: 3em;
            top: -2em;
        }


        .red::after,
        .amber::after,
        .green::after {
            content: " ";
            display: block;
            height: 1em;
            width: 1em;
            position: relative;
            left: 1.4em;
            top: 0em;
        }

        .amber::after {
            height: 0;
            width: 0;
            border: .58em solid transparent;
            /* for eq Triangle  ratio is 1:1.16. 1.16/2=~.58 */
            border-bottom: 1em solid orange;
            border-top: 0em;
            /* important for positioning */
        }


        .red::after {
            background: red;
        }

        .green::after {
            background: green;
            border-radius: 1em;
        }

        .font18 {
            font-size: 18px
        }
    </style>


    <div class="row justify-content-center">
        <div class="col-lg-3 text-center">

            <div class="avatar d-flex justify-content-center">
                <div class="user-icon" id="user-icon">
                    <span class="green"> </span>
                </div>

            </div>
            <div class="info">
                <p class=" badge badge-success text-bg-dark" id="name">Online</p><br>
                <span class="text-success badge badge-success" id="text">Online</span>
            </div>

        </div>
        <div class="col-lg-9">
            <div class="card">
                <div class="col-lg-12">
                    <table class="bordered table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="particpents">

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div> 

    </div>
@endsection


@section('page-script')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const playButton = document.getElementById('playButton');
        var notificationSound = $("#notification-sound")[0];
        var id = "{{ $id }}";
        var audioToneUrl = '';

        const audioElement = new Audio('https://kahayfaqeer-general-bucket.s3.amazonaws.com/notification1.wav');


        $(document).ready(function(){

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

                
                $("#user-icon").find('span').removeClass('red').addClass('green');
            } else { 
                $("#user-icon").find('span').removeClass('green').addClass('red');
            }


        });

        playButton.addEventListener('click', () => {
            audioElement.play()
                .then(() => {
                    console.log('Audio playback started');
                })
                .catch(error => {
                    console.error('Audio playback error:', error);
                });
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
                        updateParticipantsList(response.participants);
                    },
                    error: function() {
                        console.error("An error occurred while fetching the participant list.");
                    },
                });
            }

            function updateParticipantsList(participants) {

                var html = '';
                var userinfo = participants.user_info;
                // var userProfile = userinfo.profile_pic;
                // if(userProfile){
                //     $(this).find('img').attr('src',imagePath+userProfile);
                // }else{
                //     $(this).find('span > img').attr('src',defaultPath);
                // }

                if (userinfo.status == 'online') {
                    $("#text").text(userinfo.status)
                    $("#user-icon").find('span').removeClass('red').addClass('green');
                } else {
                    $("#text").text(userinfo.status)
                    $("#user-icon").find('span').removeClass('green').addClass('red');
                }
                $("#name").text(userinfo.name) 
                delete participants.user_info;
                if (participants) {
                    $.each(participants, function(key, item) {

                        var userStatus = '';
                        if (item.user_status == 'in-queue') {
                            userStatus = 'Waiting';
                        }
                        html += `<tr>
                            <td>${item.fname} ${item.lname}</td>
                            <td> ${userStatus}</td>
                            <td><button class="admit-button btn btn-info" data-id="${item.id}">Admit</button>
                                <button class="dismiss-button btn btn-danger"
                                    data-id="${item.id}">Dismiss</button>
                            </td>
                        </tr>`;
                    })
                } else {
                    html = '<tr><td></td><td>No Requests</td><td></td> <td></td></tr>';
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
                        $(this).parents('tr').fadeOut();
                }, 2000);
                AdmitRequest(participantId, 'admitted');

            });
            $(document).on("click", ".dismiss-button", function() {
                var participantId = $(this).data("id");
                AdmitRequest(participantId, 'dismissed');
            });


        });

        function playSound() {
            notificationSound.play()
                .then(() => {
                    // Sound played successfully
                })
                .catch(error => {
                    console.error('Error playing sound:', error);
                });
        }


        playButton.addEventListener('click', () => {
            playSound();
        });
    </script>
@endsection
