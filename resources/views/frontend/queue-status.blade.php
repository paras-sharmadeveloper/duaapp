@extends('layouts.guest')
@section('content')
    {{-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> --}}

    <section>
        <div class="container">
            <!-- main content -->
            <div class="main-content">

                <div class="d-flex justify-content-center py-2">
                    <a href="index.html" class="logoo  d-flex align-items-center wuto">
                      <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
                      <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? ''}}</span> -->
                    </a>
                  </div>
 
    
    <h1 class="text-center">Queue Management System </h1> 
        <div class="column first">
            <h2 class="orng">Event Date : {{ date("d-M-Y", strtotime($venueAddress->venue_date))  }}</h4>
            <h2 class="text-primary">Token Number</h2>
            <div class="ahead-number">
               AheadYou #{{ sprintf("%03s", $aheadPeople)  }}
            </div>
            <div class="queue-number">
              YouAt.  #{{ sprintf("%03s", $aheadPeople+1)  }}
              <br>
             <span>YourSlotTime. {{ date("g:i A", strtotime($userSlot->slot_time)) }}
                  </span>
            </div>

            <h3>Serving Time</h3>
            <p>{{$venueAddress->slot_duration  }} Mint</p>
            <div class="stats">
            <div class="stat-item">
                <h4>Total Served Token</h4>
                <span>{{ sprintf("%01s", $serveredPeople)  }} </span>  <!-- Replace 123 with your desired number -->
            </div>
            <div class="stat-item">
                <h4>Performance Status</h4>
                <span>Excellent</span>
            </div>
        </div>  
 
    </div>
            </div>
        </div>
        
    </section>
<style type="text/css">
   
.queue-number span {
    font-size: 14px;
    color: #000;
}
.orng{
    color: orange;
}
.column {
    box-sizing: border-box;
    padding: 20px;
    background-color: #fff;  /* White background for the column */
    border-radius: 15px;     /* Rounded corners */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);  /* Light shadow */
}
.first {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}
h6 {
    color: orange;
    font-weight: 500;
    text-align: center;
    font-size: 14px;
}
h2 {
    color: blue;
    font-weight: 700;
    text-align: center;
    margin-top: 10px;
    font-size: 24px;
}
.ahead-number {
    font-size: 20px;
    color: #1900ff;
    border: 3px solid #0048ff;
    margin: 20px 0;
    padding: 5px 4px;
    border-radius: 10px;
    font-weight: 700;
    width: 50%;
    text-align: center;
}
.queue-number {
    font-size: 34px;
    color: orange;
    border: 3px solid orange;
    margin: 20px 0;
    padding: 50px 20px;
    border-radius: 10px;
    font-weight: 700;
    width: 50%;
    text-align: center;
}
h3 {
    color: blue;
    font-weight: 500;
    text-align: center;
    margin-top: 10px;
    font-size: 20px;
}
p {
    text-align: center;
    font-weight: 400;
    font-size: 18px;
    color: #555;
}
.stats {
    display: flex;
    justify-content: space-between;
/*    background-color: #fff;*/
    border-radius: 10px;
/*    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* subtle shadow */*/
    padding: 10px;
    width: 80%;  /* matches the width of the queue-number div */
    margin-top: 20px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;  /* equally distribute the space */
    padding: 10px;
    border-radius: 8px;
}

h4 {
    color: blue;
    font-weight: 500;
    text-align: center;
    font-size: 16px;
    margin-bottom: 8px;  /* space between the text and the number/status */
}

span {
    color: orange;
    font-size: 18px;
    font-weight: 600;
}

.blue-btn {
    background-color: #004aad; /* dark blue */
    color: #ffffff; /* white text */
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    margin: 10px 0; /* gives space between buttons */
    width: 100%; /* makes buttons take the full width of the column */
    transition: background-color 0.3s; /* smooth color transition for hover effect */
}

.blue-btn:hover {
    background-color: #00367a; /* slightly darker blue for hover effect */
}
.column.second {
    background-color: transparent;  /* Clear background */
    box-shadow: none;  /* Remove shadow */
}


.column.third {
    width: 30%;
    max-height: 540px;  /* Adjust this based on your preference */
    overflow-y: auto; 
    background-color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.visitor-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.visitor-item {
    border-bottom: 1px solid #e0e0e0;
    padding: 10px 0;
}

.visitor-item h4 {
    color: blue;
    margin-bottom: 5px;
}

.visitor-item p {
    color: black;
    margin-bottom: 5px;
}

.booking-details {
    display: flex;
    justify-content: space-between; 
    align-items: center;
}

.booking-id {
    color: orange;
}

.slot-time {
    color: lightgrey;
    display: flex;
    align-items: center;
}

.slot-time i {
    margin-right: 5px;
}
.column.second {
    width: 30%;
}
/* Tablet: Stacking columns vertically */
@media only screen and (max-width: 992px) { 
    .container {
        flex-direction: column;
    }

    .column.first,
    .column.second,
    .column.third {
        width: 100%;
        margin-bottom: 20px; /* Space between columns when stacked */
    }

    .blue-btn {
        width: 48%; /* Allow two buttons side by side with a little space */
        margin-right: 4%;
        margin-bottom: 10px;
    }

    .blue-btn:nth-child(even) {
        margin-right: 0; /* Reset margin for even buttons */
    }
    .queue-number {
            font-size: 32px;
            color: orange;
            border: 3px solid orange;
            margin: 20px 0;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            width: 100%;
            text-align: center;
    }

    .ahead-number {
    font-size: 21px;
    color: #1900ff;
    border: 3px solid #0048ff;
    margin: 20px 0;
    padding: 5px 4px;
    border-radius: 10px;
    font-weight: 700;
    width: 100%;
    text-align: center;
}
.container {
    display: flex;
    width: 100%;
    padding: 4px;
}
h1.text-center {
    text-align: center;
    font-size: 23px;
}
.queue-number span {
    font-size: 20px;
    color: #000;
}
}

/* Mobile: Full-width columns, more space adjustments */
@media only screen and (max-width: 768px) { 
    .blue-btn {
        width: 100%;
        margin-right: 0;
        margin-bottom: 10px;
    }
    .logoo img {
    height: 100px;
    width: 100px;
}
}
</style>
@endsection