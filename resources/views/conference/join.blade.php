@extends('layouts.app')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'poppins', sans-serif;
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
        padding: 30px 40px 50px;
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
        justify-content: center;
    }

    .contarols img {
        width: 40px;
        margin: 20px 10px;
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

        .you { background: transparent !important;}
         
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
            margin: 10px 5px !important;
        }

        .contarols .call-icon {
            width: 50px !important;
        }
    }

    /* Mobile styles */

    @media screen and (max-width: 480px) {
        .you { background: transparent !important;}

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
</style>

<div class="headedd">
    <div class="container-fluid">
        <div class="top-iconsa">
            <!-- <img src="https://i.postimg.cc/cCpcXrSV/search.png"> -->
            <!-- <img src="https://i.postimg.cc/Pqy2TXWw/menu.png"> -->
        </div>
        <div class="row">
            <div class="col-1 you">
                <div id="local-video">
                    <img src="https://i.postimg.cc/521rVkhD/image.png" class="host-img">
                </div>
               
                <div class="contarols">
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
                    {{-- <img src="https://i.postimg.cc/3NVtVtgf/chat.png"> --}}
                    {{-- <img src="https://i.postimg.cc/BQPYHG0r/disconnect.png"> --}}
                    {{-- <img src="https://i.postimg.cc/fyJH8G00/call.png" class="call-icon"> --}}
                    {{-- <img src="https://i.postimg.cc/bJFgSmFY/mic.png"> --}}
                    {{-- <img src="https://i.postimg.cc/Y2sDvCJN/cast.png"> --}}
                </div>
            </div>
            <div class="col-2">
                <div class="joined">
                    <p>People Joined</p>
                    <div>
                        <div id="remote-video">
                            <img src="https://i.postimg.cc/WzFnG0QG/people-1.png">
                        </div>
                       
                        <!-- <img src="https://i.postimg.cc/fRhGbb92/people-2.png">
                        <img src="https://i.postimg.cc/02mgxSbK/people-3.png">
                        <img src="https://i.postimg.cc/K8rd3y7Z/people-4.png">
                        <img src="https://i.postimg.cc/HWFGfzsC/people-5.png"> -->
                    </div>
                </div>
                <div class="invite">
                    <p>Invite More People</p>
                    <div>
                        <img src="https://i.postimg.cc/7LHjgQXS/user-1.png">
                        <img src="https://i.postimg.cc/q71SQXZS/user-2.png">
                        <img src="https://i.postimg.cc/h4kwCGpD/user-3.png">
                        <img src="https://i.postimg.cc/GtyfL0hn/user-4.png">
                        <img src="https://i.postimg.cc/FFd8gSbC/user-5.png">
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
                            if (remoteVideo.find("video").length > 0 && remoteVideo.find("audio") .length > 0) {
                                remoteVideo.empty(); 
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