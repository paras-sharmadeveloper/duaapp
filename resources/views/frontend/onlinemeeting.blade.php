@extends('layouts.guest')

@section('content')
    <div class="d-flex justify-content-center py-4">
        <a href="index.html" class="logoo  d-flex align-items-center wuto">
            <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
        </a>
    </div>

    <style>
        .active {
            opacity: 1;
            background: #4d6181
        }


        .invite,
        .joined,
        .you {
            background: #182842;
            border-radius: 15px;
            color: #fff
        }

        .container {
            margin-left: 10px;
            padding: 0 2.5%
        }

        .top-icons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 25px 0
        }

        .invite,
        .joined {
            padding: 10px 5px
        }

        .top-icons img {
            width: 25px;
            margin-left: 40px;
            cursor: pointer
        }

        .row {
            margin-top: 15px;
            display: flex;
            justify-content: space-between
        }

        .col-1 {
            flex-basis: 65%
        }

        .col-2 {
            flex-basis: 33%
        }

        .host-img,
        video {
            width: 100%;
            border-radius: 15px
        }

        .contarols {
            display: flex;
            align-items: center;
            justify-content: center
        }

        .contarols img {
            width: 40px;
            cursor: pointer;
            transition: transform .5s
        }

        .invite,
        .invite img,
        .joined div {
            margin-top: 20px
        }

        .contarols .call-icon {
            width: 70px
        }

        .contarols img:hover {
            transform: translateY(-10px)
        }

        .joined div {
            grid-template-columns: auto auto auto;
            grid-gap: 20px
        }

        .joined img {
            width: 100%;
            border-radius: 10px;
            cursor: pointer
        }

        .you {
            /* padding: 30px 40px 50px */
        }

        .joined p {
            font-size: 25px;
            text-align: center;
        }

        @media screen and (max-width:767px) {
            .joined p {
                font-size: 28px;
                text-align: center;
            }

            .logoo img {
                height: 80px !important;
                width: 90px !important;
            }

            .container-fluid {
                padding: 0px 20px !important;
                flex-direction: column;
            }

            .you {
                background: 0 0 !important;
                order: 2;
            }

            .recipient {
                flex: 1;
                order: 1;
            }

            video {
                width: 100% !important;
            }

            .container {
                margin-left: 0 !important;
                padding: 0 5% !important
            }

            .row {
                flex-direction: column !important
            }

            .col-1,
            .col-2 {
                width: 100% !important
            }

            .contarols {
                width: row !important
            }

            .contarols img {
                width: 30px !important;
                margin: 2px 8px !important
            }

            .contarols .call-icon {
                width: 50px !important
            }
        }

        @media screen and (max-width:480px) {
            .logoo img {
                height: 80px !important;
                width: 90px !important;
            }

            .you,
            video {
                padding: 20px 20px 30px !important;
            }

            div#local-video {
                text-align: center !important;
            }

            div#local-video img {
                height: 200px !important;
                width: 200px !important;
            }

            div#remote-video video {
                max-height: 400px !important;
            }

            .col-1 .contarols img,
            .contarols img {
                margin: 10px 5px !important
            }

            .you {
                background: 0 0 !important
            }

            .contarols img {
                width: 20px !important
            }

            .joined img {
                width: 100% !important
            }
        }

        .inactive {
            display: none
        }

        .active {
            display: block !important
        }

        div#append-pending-list {
            display: flex;
            justify-content: space-between
        }

        .base-timer {
            position: relative;
            width: 100px;
            height: 100px;
            margin: auto;
        }

        .base-timer__svg {
            transform: scaleX(-1);
        }

        .base-timer__circle {
            fill: none;
            stroke: none;
        }

        .base-timer__path-elapsed {
            stroke-width: 6px;
            stroke: #efefef;
        }

        .base-timer__path-remaining {
            stroke-width: 4px;
            stroke-linecap: round;
            transform: rotate(90deg);
            transform-origin: center;
            transition: 1s linear all;
            fill-rule: nonzero;
            stroke: currentColor;
        }

        .base-timer__path-remaining.green {
            color: #39b37d;
        }

        .base-timer__path-remaining.orange {
            color: orange;
        }

        .base-timer__path-remaining.red {
            color: red;
        }

        .base-timer__label {
            position: absolute;
            width: 100px;
            height: 90px;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .asktojoin-main {
            text-align: center;
        }

        button#asktojoin {
            padding: 20px 100px;
            cursor: pointer;
        }
    </style>


    {{-- <div class="wrapper text-center" id="loader-content">
        @if ($isMeetingInProgress)
            <h1>Please wait<span class="dot">...</span></h1>
            <p>People Ahead You {{ $aheadCount }}.</p>
            <p>People Served {{ $servedCount }}.</p>
            servedCount
            <p>Approx time will be : {{ $estimatedWaitTime }} Minutes </p>
            <div class="icons">
                <a href=""><i class="fa fa-twitter"></i></a>
                <a href=""><i class="fa fa-youtube-play"></i></a>
                <a href=""><i class="fa fa-paper-plane"></i></a>
            </div>
        @else
            <p>Meeting Start In .</p>
            <h1>{{ $timeRemaining }}<span class="dot">...</span></h1>
        @endif
    </div> --}}




    <div class="row" id="main-content">

        {{-- <div class="row">
            <div class="col-md-12 text-center">
                <div id="revese-timer" data-minute="{{ $timePerSlot }}"></div>
                <span class="text-danger counter-span" style="display: none">
                    Call auto disconect when time ends</span>
            </div>
        </div> --}}



        <div class="col-lg-6  you">
            <div class="joined">
                <p>You</p>
                <div>
                    <div id="local-video">
                        <video id="video" playsinline autoplay></video>
                        {{-- <img src="https://i.postimg.cc/WzFnG0QG/people-1.png"> --}}
                    </div>
                </div>
                <div class="contarols">
                    <button class="btn btn-default local-vedio mute-button">
                        <img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XsUocURQA0CMW6VYWVyeQSq0MUQQLIRDyB4mlfoBgoYVNPiGkTGMKiX9h4wfYiIUmgaSws4i7nSKkCoaBCTwGd2bMru8tmAsDy+Vx93Bn5t03/I+HjUns4RjbEsdTfMcVDnCLldSYLl7gSQHaSoHJ8A09LAT5HLSTCtMtYZKAsgCT3yYpQVkNJiooa4CJBsoaYoYKGsc63uND6fqBSzxvUGdooP2i2F3XT8w3rDM00C98wliQWyz+4O096pRBM5jts3YCy1WF3pVyr4r88gCgvPPXeFla18EpLmKD2jjBDV5X5KKBwm7knXoT/C53LRoo7MptXWceJagzSresPWoP9W7Na3/+Lxvj6gCgZ5jrs7aFpX6FPleMjl6DodoPNNBwXasYrr2GqCjHj2l8veP8nAx0H1TUI+w0vtSgoh/yp2pQST6DpipQSUB/UWej8qEY7rZnweF/ogBtShidYFge4neDreHBo4WPOMJGaoxHEX8AoZKkhPAxXggAAAAASUVORK5CYII=">
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
            </div>
        </div>
        <div class="col-lg-6 asktojoin-main">

            <div class="asktojoin">
                <h3> Ready to join?</h3>


                @if (!empty($vistor) && $vistor->user_status == 'no_action')
                    <div class="col-lg-12 text-center mt-5">
                        <button class="btn btn-primary" id="asktojoin" data-id="{{ $vistor ? $vistor->id : 0 }}">
                            Ask To Join
                        </button>
                        <div class="text alert alert-info mt-2 asktojoin-response" style="display: none">
                            Please Wait While Host Approve Your Request
                        </div>
                    </div>
                @else
                    <div class="text alert alert-info mt-2 asktojoin-response" style="display: none">
                        Please Wait While Host Approve Your Request
                    </div>
                @endif

            </div>


        </div>
        <div class="col-lg-6 recipient" style="display: none">
            <div id="remote-video">
                <img src="https://i.postimg.cc/521rVkhD/image.png" class="host-img">
            </div>
        </div>

    </div>
@endsection


@section('page-script')
    <script>
        document.title = "KahayFaqeer.com| Online Meeting";
    </script>
    <script>
        document.addEventListener("touchstart", function() {}, true);
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.0.0/twilio-video.min.js"></script>

    <script>
        var accessToken = "{{ $accessToken }}";
        var roomName = "{{ $roomName }}";
        var isMeetingHaveFifiten = "{{ $isFifteenMinutesRemaining }}"
        var visitorId = "{{ $vistor ? $vistor->id : 0 }}"
        let twillioRoom; // Declare room as a global variable
        var timeSlot = "{{ $timePerSlot }}"
        var intervalId = setInterval(function() {
            checkParticipantStatus(visitorId, intervalId);
        }, 2500);
        if (accessToken && roomName) {
            checkParticipantStatus(visitorId, intervalId);
        } else {
            console.error('Access token or room name is missing.');
        }




        function checkParticipantStatus(visitorId, intervalId) {

            $.ajax({
                type: "POST",
                url: "{{ route('checkparticepent-status') }}", // Replace with the actual route
                dataType: "json",
                data: {
                    id: visitorId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.is_admit) {
                        $(".asktojoin-main").hide()
                        $(".recipient").show();
                        var roomName = response.roomDetails.room_name;
                        var accessToken = response.roomDetails.accessToken;
                        initializeVideoCall(accessToken, roomName)

                        timer();
                        postAjax(response.visitor.id, 'start');
                        clearInterval(intervalId);

                        $("#remote-video").find('p').hide();
                        $(".asktojoin-response").text(response.message).show()
                    } else {
                        $(".asktojoin-response").text(response.message).show()
                    }
                },
                error: function() {
                    console.error("An error occurred while fetching the participant list.");
                },
            });
        }
        $("#asktojoin").click(function() {

            $.ajax({
                type: "POST",
                url: "{{ route('asktojoin') }}", // Replace with the actual route
                data: {
                    id: $(this).attr('data-id'),
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    // Handle the response from the server

                    $("#action-btns").show();
                    $(this).hide();
                    $(".asktojoin-response").show();

                    alert("Request to join sent successfully.");
                    //  $("#response").text("You are in Waiting List. Please stay on the page will be auto connect when Host Admit Your Request");

                },
                error: function() {
                    alert("An error occurred while sending the request.");
                },
            });

        });

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
                })
                .then(function(room) {
                    var remoteVideo = $("#remote-video");
                    twillioRoom = room;
                    const localParticipant = room.localParticipant;

                    room.localParticipant.videoTracks.forEach(function(publication) {
                        if (publication.track.isEnabled) {
                            $(".action-button").show();
                            $("#local-video").find('img').hide();
                            if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio").length > 0) {
                                remoteVideo.empty(); // Remove content if both video and audio tags are found
                            }
                            $("#remote-video").find('img').hide();

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
                            if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio")
                                .length > 0) {
                                remoteVideo
                                    .empty(); // Remove content if both video and audio tags are found
                            }

                            //   $("#remote-video").empty(); 
                            $("#remote-video").find('img').hide();
                            remoteVideoContainer.appendChild(track.attach());
                        });

                        participant.tracks.forEach(publication => {

                            if (publication.isSubscribed) {
                                const track = publication.track;
                                remoteVideoContainer.appendChild(track.attach());
                            }
                        });
                    });
                    room.participants.forEach(participant => {
                        participant.on('trackSubscribed', track => {
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
                    console.error('Unable to connect:', error.message);
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
            postAjax(visitorId, 'end');
            disconnectFromVideoCall(twillioRoom);
        })

        setTimeout(function() {
            // if (isMeetingHaveFifiten) {
            $("#main-content").fadeIn();
            $("body").css("background-color", "#f6f9ff");
            $("#loader-content").fadeOut();
            // }
            // $(".alert").fadeOut();

        }, 1000);


        function postAjax(id, type) {

            var url = "{{ route('siteadmin.queue.vistor.update', ['id' => ':id']) }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    type: type,
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



        function timer() {



            if ($('#revese-timer').length) {

                const FULL_DASH_ARRAY = 283;
                const WARNING_THRESHOLD = 20;
                const ALERT_THRESHOLD = 15;

                const COLOR_CODES = {
                    info: {
                        color: "green"
                    },
                    warning: {
                        color: "orange",
                        threshold: WARNING_THRESHOLD
                    },
                    alert: {
                        color: "red",
                        threshold: ALERT_THRESHOLD
                    }
                };


                var Minute = $('#revese-timer').data('minute');
                var Seconds = Math.round(60 * Minute);
                const TIME_LIMIT = Seconds;
                let timePassed = 0;
                let timeLeft = TIME_LIMIT;
                let timerInterval = null;
                let remainingPathColor = COLOR_CODES.info.color;

                document.getElementById("revese-timer").innerHTML = `
                <div class="base-timer">
                <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <g class="base-timer__circle">
                    <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                    <path
                        id="base-timer-path-remaining"
                        stroke-dasharray="283"
                        class="base-timer__path-remaining ${remainingPathColor}"
                        d="
                        M 50, 50
                        m -45, 0
                        a 45,45 0 1,0 90,0
                        a 45,45 0 1,0 -90,0
                        "
                    ></path>
                    </g>
                </svg>
                <span id="base-timer-label" class="base-timer__label">${formatTime(
                    timeLeft
                )}</span>
                </div>`;

                startTimer();

                function onTimesUp() {

                    $('.call-cut-button').click();
                    alert("times up.. Your Call Ends automatically")
                    clearInterval(timerInterval);
                }

                function startTimer() {
                    $(".counter-span").show();
                    timerInterval = setInterval(() => {
                        timePassed = timePassed += 1;
                        timeLeft = TIME_LIMIT - timePassed;
                        document.getElementById("base-timer-label").innerHTML = formatTime(
                            timeLeft
                        );
                        setCircleDasharray();
                        setRemainingPathColor(timeLeft);

                        if (timeLeft === 0) {

                            onTimesUp();
                        }
                    }, 1000);
                }

                function formatTime(time) {
                    const minutes = Math.floor(time / 60);
                    let seconds = time % 60;

                    if (seconds < 10) {
                        seconds = `0${seconds}`;
                    }

                    return `${minutes}:${seconds}`;
                }

                function setRemainingPathColor(timeLeft) {
                    const {
                        alert,
                        warning,
                        info
                    } = COLOR_CODES;
                    if (timeLeft <= alert.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(warning.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(alert.color);
                    } else if (timeLeft <= warning.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(info.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(warning.color);
                    }
                }

                function calculateTimeFraction() {
                    const rawTimeFraction = timeLeft / TIME_LIMIT;
                    return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
                }

                function setCircleDasharray() {
                    const circleDasharray = `${(
            calculateTimeFraction() * FULL_DASH_ARRAY
        ).toFixed(0)} 283`;
                    document
                        .getElementById("base-timer-path-remaining")
                        .setAttribute("stroke-dasharray", circleDasharray);
                }

            }
        }
    </script>
@endsection
