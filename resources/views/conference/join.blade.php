@extends('layouts.app')

@section('content')
    <style>
        video {
            height: 100$;
        }

        @media (max-width: 767px) {
            video {
                height: 150px;
            }
        }

        .action-button .btn {
            border-radius: 50%;
        }
    </style>
   <div class="row justify-content-center">
    <div class="col-md-12">
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
    <div class="row" id="camera-div" style="display: none">
        <div class="col-lg-6 text-center">
            <div id="local-video"></div>
            <div class="action-button text-center">
                <button class="btn btn-default local-vedio">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XsUocURQA0CMW6VYWVyeQSq0MUQQLIRDyB4mlfoBgoYVNPiGkTGMKiX9h4wfYiIUmgaSws4i7nSKkCoaBCTwGd2bMru8tmAsDy+Vx93Bn5t03/I+HjUns4RjbEsdTfMcVDnCLldSYLl7gSQHaSoHJ8A09LAT5HLSTCtMtYZKAsgCT3yYpQVkNJiooa4CJBsoaYoYKGsc63uND6fqBSzxvUGdooP2i2F3XT8w3rDM00C98wliQWyz+4O096pRBM5jts3YCy1WF3pVyr4r88gCgvPPXeFla18EpLmKD2jjBDV5X5KKBwm7knXoT/C53LRoo7MptXWceJagzSresPWoP9W7Na3/+Lxvj6gCgZ5jrs7aFpX6FPleMjl6DodoPNNBwXasYrr2GqCjHj2l8veP8nAx0H1TUI+w0vtSgoh/yp2pQST6DpipQSUB/UWej8qEY7rZnweF/ogBtShidYFge4neDreHBo4WPOMJGaoxHEX8AoZKkhPAxXggAAAAASUVORK5CYII=">
                </button>
                <button class="btn btn-default local-vedio">
                    <img class="camera-off" style="display: none" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAACEklEQVR4nO2ZTysFURiHH2UjO2UaimJjIR+ABZYsWVrZ2ggLCh/AxgewU3aWsqPEBhshhbWtKESKXo3eq2nc687MOTNzruZXb829d94z55nfzPnzXoAz4BLwaXBdAAJcNzqMB1wpzC3QSQPLK2EclVc646i80hkHNAbsA8/Asn7n6/wiGcYrcApMA02mEKtVLjCbI0wltkxgxrSRN2CuxuwehrG9AugA5vX6AsykbWhfGwgg/lKWMIEWtO2bhHnNFfgnbSBOx7J2RjRaY5wfPIKTof78JLswNEvMmzoIHEXer8QgWTojdUAGgN3QeXemIFk5IzVAuoAN4EN/fwCWgBYbIFnASASkDVgLjWjvCtReJccIxDaMaPTqHX/Uz5/ANtDzR44xiM13RjTuQ8c7QH+MHCsgtpyRUJwAIwlyrIHYcEY0plLkWAUxdUYSTM6Zg5jAiGsgaWHERZA0MOIqSFIYcRkkCYy4DhJ3aBaXhl8TZyQyIY66ClLPGXFhiWLDGSl60WgLRopcxtt8zKTGqNUNbKozlY3Vos2NlQ1Vq5v5eW51s4Tpq3P+MHAcgf8ukYqWY4pU0i3Ar3LQoR4EBbKi5afYz/wU6CY18U1h8nYmuN6KDrfGm7P1yPNWVAzZgJkADoCXnDsflGz3gPGca825yiv/OXNUXumMo/JKZxyV95+c8UPzzHnRnTFVABNAnHwBgYvRwQNntAsAAAAASUVORK5CYII=">
                    <img class="camera-on" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB4ElEQVR4nO2XzStEURjGf0L52khsJIWVKKVQilkoxT+hWEw+QvmYrFiQLSvZzI7sWFhoFrITs5liRyglG5GyQNHVe+vtdL/M3Gmo+9SzuPe8nefpnPfc81yIEKGwqAOmgEUXxqSuCphW7xeA5rDNDAJPwJcHX6V2xmFsP0wzC8CnTPwIpAymlbCFFuBAjR8Bw2GZmVVip0C9Q03MMJRX3InQDlDmUhOaoWKgDeg3xJqBAaEttCVb58QtVdcNlP/WSBEQB57VREkZawTefZrXjx/ABlAR1FDcYYKEjFUD1zkasrkbdJvsldkDagkXFcCcMtVpjNcAfbpF2lRx2GY0LkVjQp5LgSXgTd6feJ2IYflWpBSTHqcqCI5FYxloADIOW9rkZmjfpQc6QjI0qnp1W83f7mbIOuYrwLriSA5mTEPlcgd2SA/5GsoHjpUhjciQjWiF/t0KnYjG2l8xdCsaz5I2C24oY3xkk3JxF/zY3yi9B2DsLzR1HHhxuJparcIu9SJwgMoCZ6IxL8/WBXuotO+BEsTEh1EcNnpV6rRisMaQhMGfm97GpnJ64fBbk/LhuEd0OVVmzu1V8EOlxMtso+lVgOiSzuZv1YqXk0bsWPfhKtDjEV0Ssk1WQowQgXzjG1/gIsAGld8bAAAAAElFTkSuQmCC">
                 </button>
                 <button class="btn btn-default  local-vedio">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAACJUlEQVR4nO2Uv2sUQRTHx1/EnyiIhtxMEI4UIpIELu/dGZTNvdnogfkLLBQEKyPBQgNpDhFs7PzRWFpaCcrsHSfE6sp0WpjCThSLwL5JRKKurEjIXTZ7e0cud8V+4DW7y3c+++bNCJGSktLH+KXxUz7hOd+dcFZdLDDlR3/oiez3ycljXV98rXjhjNV4nQlfWIKPrGHdagy2L/jKhBVLuGCLOLYjEkEud8BquMGE9fjFkxR8sC7cCkojAx3JWBeuWA3LzcFMuMSED9mFa9bFq76bvxhuVVg+4aXwWfju3zeES1vECD5xESl5V4TYwwSPtvvToFA4lDirNDIQlcGEv63Ge4lkLOHzuNavOqjaGfq4LNbwODbAEj5pOQvF5AManrhWeazhQXR3ymJv65ODAbugkwrZaRhvmUdo4zpUjg0gfN/OHbPijJ2IHOyN7uAf1nAn/q8I5qNlYPGb4xxNKtMkVY+UofxcohBLeL+xrfA2cJyDokOC6dEjlvBdowzOthXCGu8y4U/W8DK8IDuV2ZCayR1mgtesYc1qvCm6gSfllKdU0FBSTnVlsVRoJ/DSLYvAKFU2StX+V90o9aWp6pvel0W3qWUyJ42Uy1tOV1MZKT9XBwdPi92gMjR01pNyJUbIryp1XuwmZnj4slFqPULmVyWTmRG9oKLUbITQbdFLjJTPNs3NU9FrXgmxzyj1xpOyuijEftEP1LLZ42H12iMlpS/5C763Se/evbgOAAAAAElFTkSuQmCC">
                 </button>
                     
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div id="remote-video"></div>
            <div class="action-button  text-center">
                <button class="btn btn-default local-vedio">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XsUocURQA0CMW6VYWVyeQSq0MUQQLIRDyB4mlfoBgoYVNPiGkTGMKiX9h4wfYiIUmgaSws4i7nSKkCoaBCTwGd2bMru8tmAsDy+Vx93Bn5t03/I+HjUns4RjbEsdTfMcVDnCLldSYLl7gSQHaSoHJ8A09LAT5HLSTCtMtYZKAsgCT3yYpQVkNJiooa4CJBsoaYoYKGsc63uND6fqBSzxvUGdooP2i2F3XT8w3rDM00C98wliQWyz+4O096pRBM5jts3YCy1WF3pVyr4r88gCgvPPXeFla18EpLmKD2jjBDV5X5KKBwm7knXoT/C53LRoo7MptXWceJagzSresPWoP9W7Na3/+Lxvj6gCgZ5jrs7aFpX6FPleMjl6DodoPNNBwXasYrr2GqCjHj2l8veP8nAx0H1TUI+w0vtSgoh/yp2pQST6DpipQSUB/UWej8qEY7rZnweF/ogBtShidYFge4neDreHBo4WPOMJGaoxHEX8AoZKkhPAxXggAAAAASUVORK5CYII=">
                </button>
                <button class="btn btn-default local-vedio">
                    <img class="camera-off" style="display: none" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAACEklEQVR4nO2ZTysFURiHH2UjO2UaimJjIR+ABZYsWVrZ2ggLCh/AxgewU3aWsqPEBhshhbWtKESKXo3eq2nc687MOTNzruZXb829d94z55nfzPnzXoAz4BLwaXBdAAJcNzqMB1wpzC3QSQPLK2EclVc646i80hkHNAbsA8/Asn7n6/wiGcYrcApMA02mEKtVLjCbI0wltkxgxrSRN2CuxuwehrG9AugA5vX6AsykbWhfGwgg/lKWMIEWtO2bhHnNFfgnbSBOx7J2RjRaY5wfPIKTof78JLswNEvMmzoIHEXer8QgWTojdUAGgN3QeXemIFk5IzVAuoAN4EN/fwCWgBYbIFnASASkDVgLjWjvCtReJccIxDaMaPTqHX/Uz5/ANtDzR44xiM13RjTuQ8c7QH+MHCsgtpyRUJwAIwlyrIHYcEY0plLkWAUxdUYSTM6Zg5jAiGsgaWHERZA0MOIqSFIYcRkkCYy4DhJ3aBaXhl8TZyQyIY66ClLPGXFhiWLDGSl60WgLRopcxtt8zKTGqNUNbKozlY3Vos2NlQ1Vq5v5eW51s4Tpq3P+MHAcgf8ukYqWY4pU0i3Ar3LQoR4EBbKi5afYz/wU6CY18U1h8nYmuN6KDrfGm7P1yPNWVAzZgJkADoCXnDsflGz3gPGca825yiv/OXNUXumMo/JKZxyV95+c8UPzzHnRnTFVABNAnHwBgYvRwQNntAsAAAAASUVORK5CYII=">
                    <img class="camera-on" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB4ElEQVR4nO2XzStEURjGf0L52khsJIWVKKVQilkoxT+hWEw+QvmYrFiQLSvZzI7sWFhoFrITs5liRyglG5GyQNHVe+vtdL/M3Gmo+9SzuPe8nefpnPfc81yIEKGwqAOmgEUXxqSuCphW7xeA5rDNDAJPwJcHX6V2xmFsP0wzC8CnTPwIpAymlbCFFuBAjR8Bw2GZmVVip0C9Q03MMJRX3InQDlDmUhOaoWKgDeg3xJqBAaEttCVb58QtVdcNlP/WSBEQB57VREkZawTefZrXjx/ABlAR1FDcYYKEjFUD1zkasrkbdJvsldkDagkXFcCcMtVpjNcAfbpF2lRx2GY0LkVjQp5LgSXgTd6feJ2IYflWpBSTHqcqCI5FYxloADIOW9rkZmjfpQc6QjI0qnp1W83f7mbIOuYrwLriSA5mTEPlcgd2SA/5GsoHjpUhjciQjWiF/t0KnYjG2l8xdCsaz5I2C24oY3xkk3JxF/zY3yi9B2DsLzR1HHhxuJparcIu9SJwgMoCZ6IxL8/WBXuotO+BEsTEh1EcNnpV6rRisMaQhMGfm97GpnJ64fBbk/LhuEd0OVVmzu1V8EOlxMtso+lVgOiSzuZv1YqXk0bsWPfhKtDjEV0Ssk1WQowQgXzjG1/gIsAGld8bAAAAAElFTkSuQmCC">
                 </button>
                 <button class="btn btn-default  local-vedio">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAACXBIWXMAAAsTAAALEwEAmpwYAAACJUlEQVR4nO2Uv2sUQRTHx1/EnyiIhtxMEI4UIpIELu/dGZTNvdnogfkLLBQEKyPBQgNpDhFs7PzRWFpaCcrsHSfE6sp0WpjCThSLwL5JRKKurEjIXTZ7e0cud8V+4DW7y3c+++bNCJGSktLH+KXxUz7hOd+dcFZdLDDlR3/oiez3ycljXV98rXjhjNV4nQlfWIKPrGHdagy2L/jKhBVLuGCLOLYjEkEud8BquMGE9fjFkxR8sC7cCkojAx3JWBeuWA3LzcFMuMSED9mFa9bFq76bvxhuVVg+4aXwWfju3zeES1vECD5xESl5V4TYwwSPtvvToFA4lDirNDIQlcGEv63Ge4lkLOHzuNavOqjaGfq4LNbwODbAEj5pOQvF5AManrhWeazhQXR3ymJv65ODAbugkwrZaRhvmUdo4zpUjg0gfN/OHbPijJ2IHOyN7uAf1nAn/q8I5qNlYPGb4xxNKtMkVY+UofxcohBLeL+xrfA2cJyDokOC6dEjlvBdowzOthXCGu8y4U/W8DK8IDuV2ZCayR1mgtesYc1qvCm6gSfllKdU0FBSTnVlsVRoJ/DSLYvAKFU2StX+V90o9aWp6pvel0W3qWUyJ42Uy1tOV1MZKT9XBwdPi92gMjR01pNyJUbIryp1XuwmZnj4slFqPULmVyWTmRG9oKLUbITQbdFLjJTPNs3NU9FrXgmxzyj1xpOyuijEftEP1LLZ42H12iMlpS/5C763Se/evbgOAAAAAElFTkSuQmCC">
                 </button>
                     
            </div>
        </div>

    </div>
    @php
        $accessToken = session()->has('accessToken') ? session()->get('accessToken') : '';
        $roomName = session()->has('roomName') ? session()->get('roomName') : '';
    @endphp
    <div class="card-body">
        @if (!session()->has('enable'))
            <form method="POST" action="{{ route('join.conference.post', request()->route('roomId')) }}">
                @csrf

                <div class="form-group">
                    <label for="participantName">Your Name</label>
                    <input type="text" class="form-control" id="participantName" name="participantName" required>
                </div>

                <button type="submit" class="btn btn-primary mt-2">Join Conference</button>

            </form>
        @endif
    </div>

    </div>
</div>

</div>





@endsection



@section('page-script')
    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.0.0/twilio-video.min.js"></script>

    <script>
        var accessToken = "{{ $accessToken }}";
        var roomName = "{{ $roomName }}";

        // Check if accessToken and roomName are provided
        if (accessToken && roomName) {
            initializeVideoCall(accessToken, roomName);
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
                })
                .then(function(room) {
                    console.log('Connected to room:', room.name);

                    const localParticipant = room.localParticipant;
                    console.log(`Connected to the Room as LocalParticipant "${localParticipant.identity}"`);


                    // Handle local participant (your own video)
                    room.localParticipant.videoTracks.forEach(function(publication) {
                        console.log("publication", publication)
                        if (publication.track.isEnabled) {
                            const track = publication.track;
                            const localMediaContainer = document.createElement('div');
                            localMediaContainer.appendChild(track.attach());
                            localVideoContainer.appendChild(localMediaContainer);
                        }
                    });

                    room.on('participantConnected', participant => {
                        console.log(`Participant "${participant.identity}" connected`);

                        participant.tracks.forEach(publication => {
                            if (publication.isSubscribed) {
                                const track = publication.track;
                                remoteVideoContainer.appendChild(track.attach());
                            }
                        });

                        participant.on('trackSubscribed', track => {
                            remoteVideoContainer.appendChild(track.attach());
                        });
                    });

                    room.participants.forEach(participant => {
                        participant.tracks.forEach(publication => {
                            if (publication.track) {
                                remoteVideoContainer.appendChild(publication.track.attach());
                            }
                        });

                        participant.on('trackSubscribed', track => {
                            remoteVideoContainer.appendChild(track.attach());
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
    </script>
@endsection
