@extends('layouts.guest')
@section('content')
    <style>

         body{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    /* font-family: 'Roboto'; */
    font-family: Arial, Helvetica, sans-serif;
}

.container-fluid{
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
    padding: 30px;
    border-radius: 12px;
    font-weight: bold;
}
tr th {
    font-weight: bold;
}

.row>* {
    padding: 0;
}
.first_part{
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
.meeting-awating td{
    /* background-color: antiquewhite */
}


 
td, th {
    border: 1px solid;
    border-radius: 12px;
    font-weight: bold;
}
    </style>
      <div class="container-fluid" data-ring="ringed" >
        <div class="row">
            <audio id="notificationTune">
                <source src="notification.mp3" type="audio/mp3">
                Your browser does not support the audio tag.
            </audio>
            
            <!-- Add a button to start the token system -->
            <button onclick="startTokenSystem()">Start Token System</button>
            <div class="col-lg-12 col-md-12 col-sm-12 first_part">
                <table>
                    <thead>
                        <tr>
                            <th class="no_one">Token No.</th>
                            <th class="no_two">Info</th>
                            <th class="no_two">Status</th>
                        </tr>
                        
                    </thead>
                    
                    <tbody id="current-user-listing">
                        
                    </tbody>
                    
                </table>
            </div>
            <!-- <div class="col-lg-8 col-md-8 col-sm-12 second_part">
                <div class="heading"><h2>nmc <strong>royal hospital</strong></h2></div>
                <div class="bg_image"></div>
            </div> -->
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        var url = "{{ route('waiting-queue', request()->id) }}";
        getList();
        setInterval(() => {
            getList(); 
        }, 2500);

        let tokenCounter = 1;

    function startTokenSystem() {
        // Play the notification tune
        playNotificationTune();

        // Speak the active token number
        speakTokenNumber(tokenCounter);

        // Increment the token counter for the next token
        tokenCounter++;
    }

    function playNotificationTune() {
        // Get the audio element and play the notification tune
        let audio = document.getElementById('notificationTune');
        audio.play();
    }

       
        function speakTokenNumber(tokenNumber) {
            // Use the Web Speech API to speak the token number
            if ('speechSynthesis' in window) {
                let message = new SpeechSynthesisUtterance(`Token number ${tokenNumber}`);
                window.speechSynthesis.speak(message);
            } else {
                // Fallback for browsers that do not support the Web Speech API
                alert(`Token number ${tokenNumber}`);
            }
        }

        function getList() {
            var html = '';
            $.ajax({
                url: url, // Update the URL to your Laravel endpoint
                method: 'GET',

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    $.each(response.data, function(i, item) {
                        var className,textName,tokenNumber,meeting_start_at=''; 
                        if (item.user_status === 'no_action' || item.user_status === 'in-queue') {
                            className = 'meeting-awating';
                            textName = 'Awating..'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'admitted') {
                            className = 'admitted-active';
                            textName = 'Waiting'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'meeting-end') {
                            className = 'meetingend-active';
                            textName = 'Meeting End'; 
                            meeting_start_at = '00:00:00';  
                        } else if (item.user_status == 'in-meeting') {
                            className = 'meetingstart-active';
                            textName = 'Meeting Started'; 
                            meeting_start_at = item.meeting_start_at; 
                            $("#active-token").text(item.booking_number)
                            $("#active-time").text(formatTime(item.meeting_start_at))
                        }
                         
                        html+=`<tr class="${className}">
                                <td class="no_one">${item.booking_number}</td>
                                <td class="no_two">${item.fname} ${item.lname}</td>
                                <td class="no_two">${textName}</td>
                            </tr>`; 
                        
                        // html += `<div class="col-xl-12 mb-4 users-list">
                        //     <div class="card">
                        //         <div class="card-body">
                        //             <div class="d-flex justify-content-between align-items-center">
                        //                 <div class="token">
                        //                     <span class="rounded-circle text-center h2">${item.token_id}</span>
                        //                 </div>
                        //                 <div class="ms-3">
                        //                     <p class="fw-bold mb-1 h2">${item.fname} ${item.lname} d</p>
                        //                     <p class="text-muted mb-0 h6">${item.email}</p>
                        //                     <p class="text-muted mb-0 h6">${item.phone}</p>
                        //                 </div>

                        //                 <span class="${className}">${textName}</span>
                        //                 <span class="badge badge-warning rounded-pill d-inline h1" id="estimated-time-2">${formatTime(item.venue_date+ ' ' +item.slot_time)} </span>
                        //                 <span class="badge badge-warning rounded-pill d-inline h2">${item.slot_duration} Minute</span> 
                        //             </div>
                        //         </div>

                        //     </div>
                        // </div>`;
                        console.log("item", item.slot_time)
                    })
                    console.log("jke",html)
                    $("#current-user-listing").html(html)



                    console.log("response", response)
                },
                error: function(error) {


                }
            });

        }

        function formatTime(timeValue) {
            console.log(timeValue)
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
    </script>
@endsection
