@extends('layouts.app')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'poppins', sans-serif;
        }

        div#remote-video video {
            max-height: 622px;
        }

        /* Common styles for all devices */

        .active {
            opacity: 1;
            background: #4d6181;
        }

        .container {
            margin-left: 10px;
            padding: 0 2.5%;
        }

        .top-icons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 25px 0;
        }

        .invite,
        .joined {
            background: #182842;
            border-radius: 15px;
            padding: 10px 35px 10px;
            color: #fff;
        }

        .top-icons img {
            width: 25px;
            margin-left: 40px;
            cursor: pointer;
        }

        .row {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .col-1 {
            flex-basis: 65%;
        }

        .col-2 {
            flex-basis: 33%;
        }

        .host-img {
            width: 100%;
            border-radius: 15px;
        }

        .contarols {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .contarols img {
            width: 40px;
            cursor: pointer;
            transition: transform 0.5s;
        }

        .invite,
        .invite img,
        .joined div {
            margin-top: 20px;
        }

        .contarols .call-icon {
            width: 70px;
        }

        .contarols img:hover {
            transform: translateY(-10px);
        }

        .joined div {
            grid-template-columns: auto auto auto;
            grid-gap: 20px;
        }

        .joined img {
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
        }

        video {
            width: 100%;
            border-radius: 15px;
        }

        .you {
            background: #182842;
            border-radius: 15px;
            padding: 30px 40px 50px;
            color: #fff;
        }

        /* Tablet styles */

        @media screen and (max-width: 767px) {

            .you {
                background: transparent !important;
            }

            .container {
                margin-left: 0 !important;
                padding: 0 5% !important;
            }

            .row {
                flex-direction: column !important;
            }

            .col-1,
            .col-2 {
                width: 100% !important;
            }

            .contarols {
                width: row !important;
            }

            .contarols img {
                width: 30px !important;
                margin: 2px 8px !important;
            }

            .contarols .call-icon {
                width: 50px !important;
            }
        }

        /* Mobile styles */

        @media screen and (max-width: 480px) {
            .you {
                background: transparent !important;
            }

            .col-1 .contarols img {
                margin: 10px 5px !important;
            }

            .contarols img {
                width: 20px !important;
                margin: 10px 5px !important;
            }

            .joined img {
                width: 100% !important;
            }
        }

        .inactive {
            display: none;
        }

        .active {
            display: block !important;
        }

        div#append-pending-list {
            display: flex;
            justify-content: space-between;
        }
    </style>

    <div class="headedd">
        <div class="container-fluid">
            <div class="top-iconsa">
            </div>
            <div class="row">
                <div class="col-1 you">
                    <div id="remote-video">
                        <img src="https://i.postimg.cc/521rVkhD/image.png" class="host-img">
                    </div> 
                </div>
                <div class="col-2">
                    <div class="joined">
                        <p>You</p>
                        <div>
                            <div id="local-video">
                                <img src="https://i.postimg.cc/WzFnG0QG/people-1.png">
                            </div>
                        </div>
                        <div class="contarols">
                            <div class="inner1">
                                <button class="btn btn-default local-vedio mute-button">
                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XsUocURQA0CMW6VYWVyeQSq0MUQQLIRDyB4mlfoBgoYVNPiGkTGMKiX9h4wfYiIUmgaSws4i7nSKkCoaBCTwGd2bMru8tmAsDy+Vx93Bn5t03/I+HjUns4RjbEsdTfMcVDnCLldSYLl7gSQHaSoHJ8A09LAT5HLSTCtMtYZKAsgCT3yYpQVkNJiooa4CJBsoaYoYKGsc63uND6fqBSzxvUGdooP2i2F3XT8w3rDM00C98wliQWyz+4O096pRBM5jts3YCy1WF3pVyr4r88gCgvPPXeFla18EpLmKD2jjBDV5X5KKBwm7knXoT/C53LRoo7MptXWceJagzSresPWoP9W7Na3/+Lxvj6gCgZ5jrs7aFpX6FPleMjl6DodoPNNBwXasYrr2GqCjHj2l8veP8nAx0H1TUI+w0vtSgoh/yp2pQST6DpipQSUB/UWej8qEY7rZnweF/ogBtShidYFge4neDreHBo4WPOMJGaoxHEX8AoZKkhPAxXggAAAAASUVORK5CYII=">
                                </button>
                                <button class="btn
                                        btn-default local-vedio camera-toggle-button">
                                    <img class="camera-off" style="display: none"
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABfklEQVR4nO2XP0rEQBSHv429YOkJBIu18gjaaeMRBMFacNlCtBH2BFZJZ2EpNha23kHQE4ggtoJkJDADw9skM5OZuFH3wa9JZvI+fm/+vMAy/kjsARkDiQtAAcUQoPY1jFG+aKgRcCWgikVC7QKnwKvDqRFwCEw8dASsd4G5FhBSEmoLeHPMMXoHth35M+mMz4fzCKjHFpgD4MR+MBUTZ5buHFBjMV7KzPtscOUSKPXuntvqSr4gfqHb8+xYBW6b8rYBxULVAW0AT+JdEFAMlASqboGPmjUWDIROXgQenvZYs15UKqAuUE277hm4SQEUWr46mHtgrS1vKFCIU/Y5VeqymTFJgXydGltQpT6bVvoC6gLVW8lCyyevmaSLOpVTSbZ9X1C9APmWbxN4+SkgX6daL1e7/Xjw7AQnDk09Os/G9mOn5YhXiVV3eM41aD4trIrQlwdU7V1YOXXm6ABngTrWTX7uAfW/f7HQyaVT1c8pQ3HqnIFEplvZZfz++AbfcHqN26mzZAAAAABJRU5ErkJggg==">
                                     <img class="camera-on"
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB4ElEQVR4nO2XzStEURjGf0L52khsJIWVKKVQilkoxT+hWEw+QvmYrFiQLSvZzI7sWFhoFrITs5liRyglG5GyQNHVe+vtdL/M3Gmo+9SzuPe8nefpnPfc81yIEKGwqAOmgEUXxqSuCphW7xeA5rDNDAJPwJcHX6V2xmFsP0wzC8CnTPwIpAymlbCFFuBAjR8Bw2GZmVVip0C9Q03MMJRX3InQDlDmUhOaoWKgDeg3xJqBAaEttCVb58QtVdcNlP/WSBEQB57VREkZawTefZrXjx/ABlAR1FDcYYKEjFUD1zkasrkbdJvsldkDagkXFcCcMtVpjNcAfbpF2lRx2GY0LkVjQp5LgSXgTd6feJ2IYflWpBSTHqcqCI5FYxloADIOW9rkZmjfpQc6QjI0qnp1W83f7mbIOuYrwLriSA5mTEPlcgd2SA/5GsoHjpUhjciQjWiF/t0KnYjG2l8xdCsaz5I2C24oY3xkk3JxF/zY3yi9B2DsLzR1HHhxuJparcIu9SJwgMoCZ6IxL8/WBXuotO+BEsTEh1EcNnpV6rRisMaQhMGfm97GpnJ64fBbk/LhuEd0OVVmzu1V8EOlxMtso+lVgOiSzuZv1YqXk0bsWPfhKtDjEV0Ssk1WQowQgXzjG1/gIsAGld8bAAAAAElFTkSuQmCC">
                                </button>
                                <button class="btn btn-default  local-vedio call-cut-button">
                                    <img
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAACJUlEQVR4nO2Uv2sUQRTHx1/EnyiIhtxMEI4UIpIELu/dGZTNvdnogfkLLBQEKyPBQgNpDhFs7PzRWFpaCcrsHSfE6sp0WpjCThSLwL5JRKKurEjIXTZ7e0cud8V+4DW7y3c+++bNCJGSktLH+KXxUz7hOd+dcFZdLDDlR3/oiez3ycljXV98rXjhjNV4nQlfWIKPrGHdagy2L/jKhBVLuGCLOLYjEkEud8BquMGE9fjFkxR8sC7cCkojAx3JWBeuWA3LzcFMuMSED9mFa9bFq76bvxhuVVg+4aXwWfju3zeES1vECD5xESl5V4TYwwSPtvvToFA4lDirNDIQlcGEv63Ge4lkLOHzuNavOqjaGfq4LNbwODbAEj5pOQvF5AManrhWeazhQXR3ymJv65ODAbugkwrZaRhvmUdo4zpUjg0gfN/OHbPijJ2IHOyN7uAf1nAn/q8I5qNlYPGb4xxNKtMkVY+UofxcohBLeL+xrfA2cJyDokOC6dEjlvBdowzOthXCGu8y4U/W8DK8IDuV2ZCayR1mgtesYc1qvCm6gSfllKdU0FBSTnVlsVRoJ/DSLYvAKFU2StX+V90o9aWp6pvel0W3qWUyJ42Uy1tOV1MZKT9XBwdPi92gMjR01pNyJUbIryp1XuwmZnj4slFqPULmVyWTmRG9oKLUbITQbdFLjJTPNs3NU9FrXgmxzyj1xpOyuijEftEP1LLZ42H12iMlpS/5C763Se/evbgOAAAAAElFTkSuQmCC">
                                </button>
                            </div>
                            <div class="inner2"> 
                                    @php 
                                       // $status = (\Auth::user()->status == 'online') ? 'offline' : 'online'; 
                                        $status = empty(\Auth::user()->status) ? 'online' : \Auth::user()->status; 
                                    @endphp
                                    <button id="update-status" 
                                        @if($status == 'offline')
                                        class="btn btn-outline-success btn-block"                
                                        @else
                                        class="btn btn-outline-danger btn-block"    
                                       
                                        @endif
                                    data-status="{{  ($status == 'online') ?'offline':'online' }}">{{ ( $status == 'online') ? 'Hold Meeting' : 'Resume Meeting' }}</button>
                                 
                            </div>
                            
                        </div>
                    </div>

                    <div class="invite">
                        <p class="text-center">People In Queue</p>
                        <div class="append" id="append-pending-list">

                        </div>


                    </div>
                </div>
                

            </div>
        </div>
    </div>
@endsection


@section('page-script')
    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.18.0/twilio-video.min.js"></script>

    <script>
        $(document).ready(function() {
            fetchParticipants();
            setInterval(() => {
                fetchParticipants();
            }, 10000);
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var accessToken = "{{ Request::get('accessToken') }}";
        var roomName = "{{ Request::get('roomName') }}";
        var siteAdmin = "{{ Request::get('side_admin') }}"

        let twillioRoom; // Declare room as a global variable

        if (accessToken && roomName) {
            document.addEventListener("DOMContentLoaded", function() {
                initializeVideoCall(accessToken, roomName);

            });
            // navigator.mediaDevices.getUserMedia({ video: true ,audio:true})
            // .then(function(stream) {

            // })
            // .catch(function(error) {
            //     console.log("error",error)
            //     // Permission denied or an error occurred
            // });
        } else {
            console.error('Access token or room name is missing.');
        }

        function initializeVideoCall(token, roomName) {
            const localVideoContainer = document.getElementById('local-video');
            const remoteVideoContainer = document.getElementById('remote-video');
            // const videoChatWindow = document.getElementById('video-chat-window');
            $("#camera-div").show();
            // Connect to the Twilio Video room
            Twilio.Video.connect(token, {
                    video: true,
                    audio: true,
                    name: roomName,
                }).then(function(room) {

                    twillioRoom = room;
                    const localParticipant = room.localParticipant;
                    var remoteVideo = $("#remote-video");
                    room.localParticipant.videoTracks.forEach(function(publication) {
                        if (publication.track.isEnabled) {
                            $(".action-button").show();
                            $("#local-video").find('img').hide();
                            const track = publication.track;
                            const localMediaContainer = document.createElement('div');

                            localMediaContainer.appendChild(track.attach());
                            localVideoContainer.appendChild(localMediaContainer);
                        } else {
                            console.log("local2")
                            console.error('Camera track is not enabled.');
                        }
                    });

                    room.on('participantConnected', participant => {


                        participant.on('trackSubscribed', track => {
                            console.log("1")
                            if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio")
                                .length > 0) {
                                remoteVideo.empty();
                            }
                            $("#remote-video").find('img').hide();
                            remoteVideoContainer.appendChild(track.attach());
                        });

                        participant.tracks.forEach(publication => {

                            if (publication.isSubscribed) {
                                console.log("2")
                                if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio")
                                    .length > 0) {
                                    remoteVideo
                                        .empty(); // Remove content if both video and audio tags are found
                                }
                                $("#remote-video").find('img').hide();
                                const track = publication.track;
                                remoteVideoContainer.appendChild(track.attach());
                            }
                        });
                    });
                    room.participants.forEach(participant => {
                        participant.on('trackSubscribed', track => {
                            console.log("3")
                            if (track.isEnabled) {
                                if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio")
                                    .length > 0) {
                                    remoteVideo
                                        .empty(); // Remove content if both video and audio tags are found
                                }

                                $("#remote-video").find('img').hide();
                                remoteVideoContainer.appendChild(track.attach());
                            }

                        });
                    });

                    // Handle room errors
                    room.on('error', function(error) {
                        console.error('Error:', error.message);
                    });
                })
                .catch(function(error) {
                    alert("Please check that your Camera is not busy with some other application or Properly connected.Try again!")
                    console.error('Unable to connect:', error);
                });
        }

        function toggleMute(room) {

            const localParticipant = room.localParticipant;

            localParticipant.audioTracks.forEach(track => {
                console.log("track", track.track.isEnabled)
                if (track.track.isEnabled) {
                    track.track.disable();
                } else {
                    track.track.enable();
                }
            });
        }

        // Function to toggle camera on/off
        function toggleCamera(room) {
            const localParticipant = room.localParticipant;

            localParticipant.videoTracks.forEach(track => {
                if (track.track.isEnabled) {
                    track.track.disable();
                } else {
                    track.track.enable();
                }
            });
        }

        // Function to disconnect from the video call
        function disconnectFromVideoCall(room) {
            // clearInterval(admissionCheckInterval);
            room.disconnect();
        }

        $('.mute-button').click(function() {
            $(this).toggleClass('btn-danger');
            toggleMute(twillioRoom);
        })
        $('.camera-toggle-button').click(function() {
            $(this).find(".camera-off").toggleClass('active');
            $(this).find(".camera-on").toggleClass('inactive');
            toggleCamera(twillioRoom);
        })
        $('.call-cut-button').click(function() {
            $(this).toggleClass('btn-danger');
            disconnectFromVideoCall(twillioRoom);
        })

        setTimeout(function() {
            $(".alert").fadeOut();
        }, 2500);

        $("#update-status").click(function() {
            $this = $(this);
            var status = $(this).attr('data-status');
            $.ajax({
                type: 'POST',
                url: "{{ route('update.status') }}",
                data: {
                    "status": status,
                    'site_admin_id': siteAdmin
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("status", status)
                    $this.toggleClass("btn-outline-danger btn-outline-success");
                    if (status == 'online') {

                        $this.text("Hold Meeting");
                        $this.attr('data-status', 'offline')
                    } else {
                        $this.text("Resume Meeting");
                        $this.attr('data-status', 'online')
                    }

                },
                error: function(error) {
                    alert(error)
                }
            });

        })

        function fetchParticipants() {
            $.ajax({
                type: "post",
                url: "{{ route('visitor.list') }}", // Replace with the actual route
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status) {
                        updateParticipantsList(response.participants);
                    } else {
                        $("#append-pending-list").html('<p>No Pending Request</p>');
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

                    html += ` <div id="pending-list${item.id}" class="text-center" >
                                <img  src="data:image/png;base64,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">
                                <div class="button-container">
                                    <p> ${item.fname} </p>
                                    <button class="btn btn-outline-success admit-button" data-id="${item.id}" style="font-size:10px"><i
                                            class="fa fa-check"></i></button>
                                    <button class="btn btn-outline-danger dismiss-button" data-id="${item.id}" style="font-size:10px"><i
                                            class="fa fa-ban"></i></button>
                                </div>
                            </div>`;

                })
            } else {
                html = '<p>No Request</p>';
            }
            $("#append-pending-list").html(html)

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

                    $("#pending-list" + participantId), fadeOut();
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
                $("pending-list"+participantId).fadeOut();
                // $(this).parents('tr').fadeOut();
            }, 2000);
            AdmitRequest(participantId, 'admitted');

        });
        $(document).on("click", ".dismiss-button", function() {
            var participantId = $(this).data("id");
            AdmitRequest(participantId, 'dismissed');
        });
    </script>
@endsection
