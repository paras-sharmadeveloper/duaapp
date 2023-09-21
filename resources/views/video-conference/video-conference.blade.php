@extends('layouts.app')

@section('content')
<div id="local-video"></div>

<!-- Remote video container -->
<div id="remote-video"></div>

<button onclick="initializeVideoCall('{{ $accessToken }}','{{ $roomName }}')">Start Video Call</button>

@endsection

@section('page-script') 
   <!-- Include twilio-video via a module bundler or use a CDN -->
   <!-- Example using CDN -->
   <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.0.0/twilio-video.min.js"></script>


   <script>
    // Function to initialize the video call
    function initializeVideoCall(token,room) {
        // Connect to Twilio Video with the generated token
        Twilio.Video.connect(token, { video: true, audio: true, name: room}).then(function (room) {
            console.log('Connected to room:', room.name);
 
            // Handle local participant (your own video)
            const localVideoContainer = document.getElementById('local-video');
            room.localParticipant.media.attach(localVideoContainer);
 
            // Listen for new participants joining the room
            room.on('participantConnected', function (participant) {
                console.log('Participant connected:', participant.identity);
 
                // Handle remote participant (other participant's video)
                const remoteVideoContainer = document.getElementById('remote-video');
                participant.media.attach(remoteVideoContainer);
            });
        }).catch(function (error) {
            console.log('Error connecting to Twilio:', error);
        });
   }
   </script>
@endsection

