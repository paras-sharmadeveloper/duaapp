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
.fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
      <div class="container-fluid" id="curt-token" data-soundon="yes">
        <div class="row align-items-end">
            <audio id="notificationTune" >
                <source src="{{ asset('assets/mp3/door_bell.mp3') }}" type="audio/mp3"> 
            </audio>
            
            <!-- Add a button to start the token system -->
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
            <div class="row fixed-bottom">
                {{-- <div class="btn btn-info btn py-2 sound-on">Sound On</div> --}}
            </div>
             
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
        $(".sound-on").click(function(){
     
            $(this).text(function(i, text){
                if(text == 'Sound On'){
                    $("#curt-token").attr("data-soundon",'yes');
                    return text = "Sound Off";
                }else{
                    $("#curt-token").attr("data-soundon",'no');
                    return text = "Sound On";
                    
                } 
              
               
            });

            // Toggle class
            $(this).toggleClass("sound-on sound-off");
        })
        let tokenCounter = 1;
        
    function startTokenSystem() {
        // Play the notification tune
        playNotificationTune();  
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

                        var tonePlayed = $("tr-"+item.id).attr('data-tone'); 
                       
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
                         
                        html+=`<tr class="${className}" >
                                <td class="no_one">${item.booking_number}</td>
                                <td class="no_two">${item.fname} ${item.lname}</td>
                                <td class="no_two">${textName}</td>
                            </tr>`;    
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
    </script>
@endsection
