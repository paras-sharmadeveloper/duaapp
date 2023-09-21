@extends('layouts.app')

@section('content')
<div id="local-video"></div>
<div id="remote-video"></div>

<div class="card-body">
    <form method="POST" action="{{ route('join.conference.post',request()->route('roomId')) }}">
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



@php 
$accessToken = session()->has('accessToken') ? session()->get('accessToken') : '';
$roomName = session()->has('roomName') ? session()->get('roomName') : '';
@endphp

@endsection



@section('page-script')
<script src="https://media.twiliocdn.com/sdk/js/video/releases/2.0.0/twilio-video.min.js"></script>

<script>
    var accessToken = "{{ $accessToken }} ";
    var roomName = "{{ $roomName }} ";
    console.log("accessToken",accessToken)
    if(accessToken!==' ' && roomName !==' '){
        
        initializeVideoCall(accessToken, roomName); 
    }

function initializeVideoCall(token, room) {
    Twilio.Video.connect(token, { video: true, audio: true, name: room }).then(function (room) {
        console.log('Connected to room:', room.name);

        // Handle local participant (your own video)
        const localVideoContainer = document.getElementById('local-video');
        room.localParticipant.videoTracks.forEach(function (publication) {
            // Check if the track is enabled before attaching it
            if (publication.isTrackEnabled) {
                console.log("local")
                const track = publication.track;
                const localMediaContainer = document.createElement('div');
                localMediaContainer.appendChild(track.attach());
                localVideoContainer.appendChild(localMediaContainer);
            }
        });


        room.on('participantConnected', function (participant) {
                    console.log('Participant connected:', participant.identity);

                    // Handle remote participant (other participant's video)
                    const remoteVideoContainer = document.getElementById('remote-video');
                    participant.videoTracks.forEach(function (track) {
                        // Attach remote participant's video track to the container
                        const RemoteMediaContainer = document.createElement('div');
                        RemoteMediaContainer.appendChild(track.attach());
                        remoteVideoContainer.appendChild(RemoteMediaContainer);
                    });
                });

         

        // Listen for new participants joining the room
        // room.on('participantConnected', function (participant) {
        //     console.log('Participant connected:', participant.identity);
        //     const remoteVideoContainer = document.getElementById('remote-video');

        //     participant.videoTracks.forEach(function (track) {
        //         // Attach remote participant's video track to the container
        //         remoteVideoContainer.appendChild(track.attach());
        //     });

        //     // Handle remote participant (other participant's video)
        //     const remoteVideoContainer = document.getElementById('remote-video');
        //     participant.videoTracks.forEach(function (track) {
        //         console.log("track",track)
        //         // const remoteMediaContainer = document.createElement('div');
        //         // remoteMediaContainer.appendChild(track.attach());
        //         // remoteVideoContainer.appendChild(remoteMediaContainer);
        //     });
        // });
    }).catch(function (error) {
        console.log('Error connecting to Twilio:', error);
    });
}
</script>
@endsection
