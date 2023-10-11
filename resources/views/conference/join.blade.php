@extends('layouts.app')

@section('content')
    <style>
        video {
            width: 450px;
            height: 450px;
        }

        @media (max-width: 767px) {
            #local-video video {
                height: 150px;
                max-height: 200px;
            }

            #remote-video video {
                height: 250px;
                max-height: 300px;

            }

            div#local-video img,
            #remote-video img {
                height: 250px;
                max-height: 300px;
                bottom: 0;
            }
        }

        .action-button .btn {
            border-radius: 50%;
        }

        div#local-video img,
        #remote-video img {
            height: 400px;
            bottom: 0;
        }

        img.camera-off.active {
            display: block !important;
        }

        img.camera-on.inactive {
            display: none !important;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">Join Meeting </div>

               
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
                <div class="row d-flex justify-content-evenly" id="camera-div" style="display: none">

                    <div class="col-lg-6 text-center">
                        <div id="local-video">
                            <img class="veio" src="/assets/theme/img/avatar.png">
                        </div>
                        <div class="info">
                            <label for="username"> You </label>
                            <hr>
                        </div>
                        <div class="action-button text-center" style="display: none">
                            <button class="btn btn-default local-vedio mute-button">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XsUocURQA0CMW6VYWVyeQSq0MUQQLIRDyB4mlfoBgoYVNPiGkTGMKiX9h4wfYiIUmgaSws4i7nSKkCoaBCTwGd2bMru8tmAsDy+Vx93Bn5t03/I+HjUns4RjbEsdTfMcVDnCLldSYLl7gSQHaSoHJ8A09LAT5HLSTCtMtYZKAsgCT3yYpQVkNJiooa4CJBsoaYoYKGsc63uND6fqBSzxvUGdooP2i2F3XT8w3rDM00C98wliQWyz+4O096pRBM5jts3YCy1WF3pVyr4r88gCgvPPXeFla18EpLmKD2jjBDV5X5KKBwm7knXoT/C53LRoo7MptXWceJagzSresPWoP9W7Na3/+Lxvj6gCgZ5jrs7aFpX6FPleMjl6DodoPNNBwXasYrr2GqCjHj2l8veP8nAx0H1TUI+w0vtSgoh/yp2pQST6DpipQSUB/UWej8qEY7rZnweF/ogBtShidYFge4neDreHBo4WPOMJGaoxHEX8AoZKkhPAxXggAAAAASUVORK5CYII=">
                            </button>
                            <button class="btn btn-default local-vedio camera-toggle-button">
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
                    <div class="col-lg-6 text-center">
                        <div id="remote-video">
                            <img class="veio" src="/assets/theme/img/avatar.png">
                        </div>
                        <div class="info">
                            <label for="username"> Participant </label>
                            <hr>
                        </div>

                    </div>
                   
                    
 
                </div>
                <div class="row  mt-5">
                    <div class="col-lg-12 text-center  py-4">
                        @php 
                            $status = (\Auth::user()->status == 'online') ? 'offline' : 'online'; 
                        @endphp
                        <button id="update-status" 
                            @if($status == 'offline')
                            class="btn btn-outline-success btn-block"                
                            @else
                            class="btn btn-outline-danger btn-block"    
                           
                            @endif
                        data-status="{{  $status }}">{{ ( $status == 'online') ? 'Hold Meeting' : 'Resume Meeting' }}</button>
                    </div>
                </div>

            </div>
        </div> 

    </div>

@endsection


@section('page-script')
<script src="https://media.twiliocdn.com/sdk/js/video/releases/2.18.0/twilio-video.min.js"></script>
    
    <script>
           
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
            console.log("accessToken",token)
            console.log("roomName",roomName)
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
                                remoteVideo
                            .empty(); // Remove content if both video and audio tags are found
                            }
                            $("#remote-video").find('img').hide();
                            remoteVideoContainer.appendChild(track.attach());
                        });

                        participant.tracks.forEach(publication => {

                            if (publication.isSubscribed) {
                                console.log("2")
                                if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio").length > 0) {
                                   remoteVideo.empty(); // Remove content if both video and audio tags are found
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
                                remoteVideo.empty(); // Remove content if both video and audio tags are found
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

        $("#update-status").click(function(){
            $this = $(this); 
            var status = $(this).attr('data-status'); 
            $.ajax({
                type: 'POST',
                url: "{{ route('update.status') }}",
                data: { 
                    "status": status,
                    'site_admin_id':siteAdmin
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) { 
                    console.log("status",status)
                    $this.toggleClass("btn-outline-danger btn-outline-success");
                    if(status == 'online'){
    
                        $this.text("Hold Meeting");
                        $this.attr('data-status','offline')
                    }else{
                        $this.text("Resume Meeting");
                        $this.attr('data-status','online')
                    }
                   
                },
                error:function(error){
                    alert(error)
                }
            });

        })
 
    </script>
@endsection
