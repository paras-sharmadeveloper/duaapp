@extends('layouts.app')

@section('content')
    <style>
        video {
            height: 300px;
        }

        @media (max-width: 767px) {
            video {
                height: 150px;
            }
        }
    </style>
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
    <div class="d-flex justify-content-around">
        <div id="local-video"></div>
        <div id="remote-video"></div>
    </div>
    @php
        $accessToken = session()->has('accessToken') ? session()->get('accessToken') : '';
        $roomName = session()->has('roomName') ? session()->get('roomName') : '';
    @endphp
    <div class="card-body">
        <form method="POST" action="{{ route('join.conference.post', request()->route('roomId')) }}">
            @csrf

            <div class="form-group">
                <label for="participantName">Your Name</label>
                <input type="text" class="form-control" id="participantName" name="participantName" required>
            </div>

            <button type="submit" class="btn btn-primary">Join Conference</button>
        </form>
    </div>

    <button onclick="initializeVideoCall('{{ $accessToken }}','{{ $roomName }}')">Start Video Call</button>
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

            // Connect to the Twilio Video room
            Twilio.Video.connect(token, {
                    video: true,
                    audio: true,
                    name: roomName,
                })
                .then(function(room) {
                    console.log('Connected to room:', room.name);

                    // Handle local participant (your own video)
                    room.localParticipant.videoTracks.forEach(function(publication) {
                        if (publication.track.isEnabled) {
                            const track = publication.track;
                            const localMediaContainer = document.createElement('div');
                            localMediaContainer.appendChild(track.attach());
                            localVideoContainer.appendChild(localMediaContainer);
                        }
                    });

                    room.on('participantConnected', function(participant) {
                        console.log('Participant connected:', participant.identity);

                        // Subscribe to remote video tracks
                        participant.on('trackSubscribed', function(track) {
                            console.log("track",track)
                            if (track.kind === 'video') {
                                const remoteMediaContainer = document.createElement('div');
                                remoteMediaContainer.appendChild(track.attach());
                                remoteVideoContainer.appendChild(remoteMediaContainer);
                            }
                        });

                        // Subscribe to existing tracks for all participants
                        room.participants.forEach(function(existingParticipant) {
                            existingParticipant.tracks.forEach(function(publication) {
                                console.log("publication2",publication)
                                if (publication.isSubscribed && publication.track.kind === 'video') {
                                    const remoteMediaContainer = document.createElement('div');
                                    remoteMediaContainer.appendChild(publication.track.attach());
                                    remoteVideoContainer.appendChild(remoteMediaContainer);
                                }
                            });
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
