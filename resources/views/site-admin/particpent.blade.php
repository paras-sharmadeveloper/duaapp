@extends('layouts.app')

@section('content')


    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="col-lg-12"> 
                    <h1> Test</h1> 
                    <table class="bordered table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="particpents">

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('page-script')

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('6353ed756c194326029e', {
      cluster: 'mt1'
    });

    var channel = pusher.subscribe('notifications-channel');
    pusher.connection.bind('connected', function() {
    console.log('Pusher connected');
});
    channel.bind('my-event', function(data) {
        console.log("data",data)
      alert(JSON.stringify(data));
    });
  </script>

  <script>

        $(document).ready(function() {
            fetchParticipants(); 
        setInterval(() => {
            fetchParticipants();
        }, 10000);
            function fetchParticipants() {
                $.ajax({
                    type: "post",
                    url: "{{ route('visitor.list') }}", // Replace with the actual route
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        updateParticipantsList(response.participants);
                    },
                    error: function() {
                        console.error("An error occurred while fetching the participant list.");
                    },
                });
            }

            function updateParticipantsList(participants) {

                var html = '';
                if(participants.length > 0){
                $.each(participants, function(key, item) {

                    var userStatus = '';
                    if (item.user_status == 'in-queue') {
                        userStatus = 'Waiting';
                    }
                    html += `<tr>
                            <td>${item.fname} ${item.lname}</td>
                            <td> ${userStatus}</td>
                            <td><button class="admit-button btn btn-info" data-id="${item.id}">Admit</button>
                                <button class="dismiss-button btn btn-danger"
                                    data-id="${item.id}">Dismiss</button>
                            </td>
                        </tr>`;
                })
            }else{
                html='<tr><td></td><td>No Requests</td><td></td> <td></td></tr>'; 
            }
            console.log("html",html)
                $("#particpents").html(html)

            }
            // Function to fetch the list of participants
            function AdmitRequest(participantId, action) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('asktojoin') }}", // Replace with the actual route
                    data: {
                        id: participantId,
                        action: action,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response from the server

                        // Request successful
                        alert("Request to join sent successfully.");

                    },
                    error: function() {
                        // Request failed
                        alert("An error occurred while sending the request.");
                    },
                });
            }

            $(document).on("click", ".admit-button", function() {
                var participantId = $(this).data("id");
                AdmitRequest(participantId, 'admitted');
            });
            $(document).on("click", ".dismiss-button", function() {
                var participantId = $(this).data("id");
                AdmitRequest(participantId, 'dismissed');
            });


        });
    </script>
@endsection
