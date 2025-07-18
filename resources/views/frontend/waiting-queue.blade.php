@extends('layouts.guest')
@section('content')
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* font-family: 'Roboto'; */
            font-family: Arial, Helvetica, sans-serif;
        }

        .container-fluid {
            padding: 0px
        }

        .main {
            position: relative;
            height: 100vh;
            width: 100%;
        }

        table {
            height: 100%;
            caption-side: bottom;
            border-collapse: separate;
        }

        table {
            height: 100%;
            caption-side: bottom;
            border-collapse: separate;
            width: 100%;
        }

        tbody,
        td,
        tfoot,
        thead,
        tr {
            line-height: 1;
            border-radius: 12px;
            background-color: #ffffff;
            text-align: center;
            font-size: 6rem;
            font-weight: 500;
            color: #080808;
            padding: 20px;
        }

        th {
            text-align: inherit;
            text-align: -webkit-match-parent;
            font-size: 5rem;
            font-weight: 500;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 12px;
            font-weight: bold;
        }

        tr th {
            font-weight: bold;
        }

        .row>* {
            padding: 0;
        }

        .first_part {
            /* width: 100%; */
        }

        /* .no_one{
            width: 60%;
            max-width: 100%;
        }
        .no_two{
            width: 40%;
            max-width: 100%;
        } */
        .heading h2 {
            font-size: 30px;
            font-weight: 400;
            line-height: 1.2;
            color: #249fed;
            background-color: #ffffff;
            margin: 0px;
            padding: 24px 0px;
            text-align: center;
        }

        .bg_image {
            position: relative;
            background-image: url(../images/city.jpg);
            background-repeat: repeat;
            background-size: cover;
            height: 100vh;
            width: 100%;
        }

        .meetingstart-active td {
            background-color: #00FF00;
        }

        .meetingend-active td {
            background-color: #ff0000;
            display: none
        }

        .meeting-awating td {
            /* background-color: antiquewhite */
        }



        td,
        th {
            border: 1px solid;
            border-radius: 12px;
            font-weight: bold;
        }
    </style>
    <div class="container-fluid" id="curt-token" data-ring="" data-token="">
        <div class="row">
            <audio id="notificationTune">
                <source src="{{ asset('assets/mp3/door_bell.mp3') }}" type="audio/mp3">
                Your browser does not support the audio tag.
            </audio>
            <audio id="tokenNumberTone">
                <source id="tokenNumberToneSrc" src="" type="audio/mp3">
                Your browser does not support the audio tag.
            </audio>
            <div class="col-lg-12 col-md-12 col-sm-12 first_part">
                <table>
                    <thead>
                        <tr>
                            <th class="no_one">Token No. <h1>{{ $venueAddress->city }} /
                                    {{ date('d-M-Y', strtotime($venueAddress->venue_date)) }} </h1>
                            </th>
                            {{-- <th class="no_two">Status</th> --}}

                        </tr>

                    </thead>

                    <tbody id="current-user-listing">

                    </tbody>

                </table>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 text-center mt-5">
                <button class="btn btn-primary get-started" onclick="startTokenSystem()">Get Started</button>
            </div>
            <div style="display: none" id="soundBox">
            </div>

        </div>
    </div>
@endsection

@section('page-script')
    <script>
        var audio = null;
        var audioQueue = [];


        var url = "{{ route('waiting-queue', request()->id) }}";

        function startTokenSystem() {
            getList();
            $(".get-started").fadeOut()
            setInterval(() => {
                getList();
            }, 3000);
        }

        function playNotificationTune() {

            audio = document.getElementById('notificationTune');
            audio.play();
        }

        var isAudioPlaying = false; // flag to keep track of whether audio is playing

        // function speakTokenNumber(tokenNumber) {
        //     if (tokenNumber <= 9) {
        //         tokenNumber = '000' + tokenNumber;
        //     } else if (tokenNumber <= 99 && tokenNumber >= 10) {
        //         tokenNumber = '00' + tokenNumber;
        //     } else if (tokenNumber <= 999 && tokenNumber >= 100) {
        //         tokenNumber = '0' + tokenNumber;
        //     } else if (tokenNumber <= 1999 && tokenNumber >= 1000) {
        //         tokenNumber = tokenNumber;
        //     }

        //     var toneUrl = `https://dua-token-numbers.s3.ap-southeast-1.amazonaws.com/TOKEN-${tokenNumber}.wav`;

        //     if (!isAudioPlaying) { // Check if audio is not currently playing
        //         var audio = new Audio(toneUrl);
        //         audio.play();

        //         // Set flag to true to indicate audio is playing
        //         isAudioPlaying = true;

        //         // Add event listener to reset the flag when audio finishes playing
        //         audio.onended = function() {
        //             isAudioPlaying = false;
        //         };
        //     }
        // }

        var isAudioPlaying = false; // flag to keep track of whether audio is playing
        var audioQueue = []; // Queue to hold the audio URLs

        function speakTokenNumber(tokenNumber) {
            if (!isAudioPlaying) { // Check if audio is not currently playing
                // Construct the URL for the audio file
                if (tokenNumber <= 9) {
                    tokenNumber = '000' + tokenNumber;
                } else if (tokenNumber <= 99 && tokenNumber >= 10) {
                    tokenNumber = '00' + tokenNumber;
                } else if (tokenNumber <= 999 && tokenNumber >= 100) {
                    tokenNumber = '0' + tokenNumber;
                } else if (tokenNumber <= 1999 && tokenNumber >= 1000) {
                    tokenNumber = tokenNumber;
                }
                var toneUrl = `https://dua-token-numbers.s3.ap-southeast-1.amazonaws.com/TOKEN-${tokenNumber}.wav`;

                // Add the URL to the audio queue
                audioQueue.push(toneUrl);

                // If audio is not currently playing, start playing from the queue
                playFromQueue();
            } else {
                // If audio is already playing, add the URL to the queue
                if (tokenNumber <= 9) {
                    tokenNumber = '000' + tokenNumber;
                } else if (tokenNumber <= 99 && tokenNumber >= 10) {
                    tokenNumber = '00' + tokenNumber;
                } else if (tokenNumber <= 999 && tokenNumber >= 100) {
                    tokenNumber = '0' + tokenNumber;
                } else if (tokenNumber <= 1999 && tokenNumber >= 1000) {
                    tokenNumber = tokenNumber;
                }
                var toneUrl = `https://dua-token-numbers.s3.ap-southeast-1.amazonaws.com/TOKEN-${tokenNumber}.wav`;
                audioQueue.push(toneUrl);
            }
        }

        function playFromQueue() {
            // Check if there are audio URLs in the queue
            if (audioQueue.length > 0) {
                playNotificationTune()
                var audio = new Audio(audioQueue.shift()); // Get the first URL from the queue
                audio.play(); // Play the audio

                // Set flag to true to indicate audio is playing
                isAudioPlaying = true;

                // Add event listener to reset the flag when audio finishes playing
                audio.onended = function() {
                    isAudioPlaying = false;

                    // Play the next audio in the queue
                    playFromQueue();
                };
            }
        }

        function playSound(url) {

        }
        // Add this variable
        var UserId = null;

        function getList() {
            var html = '';
            let tunePlayed = false;

            $.ajax({
                url: url, // Update the URL to your Laravel endpoint
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    var consoleLogged = false;
                    var isRing = $("#curt-token").attr('data-ring');
                    var isToken = $("#curt-token").attr('data-token');

                    $.each(response.data, function(i, item) {

                        var className, textName, tokenNumber, meeting_start_at = '';
                        if (item.user_status === 'no_action' || item.user_status === 'in-queue') {
                            className = 'meeting-awating';
                            textName = 'Awating..';
                            meeting_start_at = '00:00:00';
                            tunePlayed = false;
                        } else if (item.user_status == 'admitted') {
                            className = 'admitted-active';
                            textName = 'Waiting';
                            meeting_start_at = '00:00:00';
                            tunePlayed = false;
                            $("#ring" + item.booking_number).remove()
                        } else if (item.user_status == 'meeting-end') {
                            className = 'meetingend-active';
                            textName = 'Meeting End';
                            meeting_start_at = '00:00:00';
                            tunePlayed = false;
                        } else if (item.user_status == 'in-meeting') {
                            className = 'meetingstart-active';
                            textName = 'Meeting Started';
                            meeting_start_at = item.meeting_start_at;

                            isRing = $("#ring" + item.booking_number).val();
                            // Check if console.log has not been triggered
                            if (isRing != 'played') {
                                //  console.log("One time",item.booking_number);

                                setTimeout(() => {

                                    speakTokenNumber(item.booking_number)
                                    $('#soundBox').append(
                                        `<input type="hidden" id="ring${item.booking_number}" name="" value="played">`
                                        );
                                }, 2000);



                            }

                            $("#active-token").text(item.booking_number)
                            $("#active-time").text(formatTime(item.meeting_start_at))
                            tunePlayed = true;
                        }

                        html +=
                            `<tr class="${className}">  <td class="no_one">${item.booking_number}</td>   </tr>`;
                    })
                    $("#current-user-listing").html(html)
                },
                error: function(error) {

                }
            });
        }

        function formatTime(timeValue) {
            // Create a Date object by combining the date part with the time value
            var date = new Date(timeValue);
            // Format the time using options
            var formattedTime = date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            return formattedTime;
        }
        document.title = "Token Status - KahayFaqeer.org";
    </script>
@endsection
