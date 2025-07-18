@extends('layouts.guest')
@section('content')


<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .statement-notes {
            font-family: 'Jameel Noori Nastaleeq', sans-serif;
        }

        

        @font-face {
                font-family: 'Jameel-Noori-Nastaleeq-Regular';
                src: url({{ public_path('assets/fonts/Jameel-Noori-Nastaleeq-Regular.ttf')}}) format('truetype');
            }

            .urdu-text {
                font-family: 'Jameel-Noori-Nastaleeq-Regular', sans-serif;
            }      
       

        span.text-center.text-success.confirm {
            font-size: 24px;
        }

       
        .venue-info h6,
        .stats h3 {
            color: #000;
        }

        .queue-number span {
            font-size: 20px;
            color: #000;
        }

        .orng {
            color: #000;
        }

        .column {
            box-sizing: border-box;
            padding: 20px;
            background-color: #fff;
            /* White background for the column */
            border-radius: 15px;
            /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Light shadow */
        }

        .first {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h6 {
            color: #000;
            /* font-weight: 500; */
            text-align: center;
            font-size: 14px;
        }

        h2 {
            color: #000;
            /* font-weight: 700; */
            text-align: center;
            margin-top: 10px;
            font-size: 24px;
        }

        .ahead-number {
            font-size: 20px;
            color: #000;
            border: 3px solid #000;
            ;
            margin: 20px 0;
            padding: 5px 4px;
            border-radius: 10px;
            font-weight: 700;
            width: 50%;
            text-align: center;
        }

        .queue-number {
            font-size: 34px;
            color: #000;
            border: 3px solid #000;
            margin: 20px 0;
            padding: 30px 10px;
            border-radius: 10px;
            font-weight: 700;
            width: 10%;
            text-align: center;
        }

        h3 {
            color: #000;
            /* font-weight: 500; */
            text-align: center;
            margin-top: 10px;
            font-size: 20px;
        }

        p {
            text-align: center;
            font-weight: 400;
            font-size: 18px;
            color: #000;
        }

        .stats {
            /* display: flex; */
            /* justify-content: space-between; */
            /*    background-color: #fff;*/
            border-radius: 10px;
            /*    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* subtle shadow */
            */ padding: 10px;
            width: 80%;
            /* matches the width of the queue-number div */
            margin-top: 20px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            /* equally distribute the space */
            padding: 10px;
            border-radius: 8px;
        }

        h4 {
            color: #000;
            font-weight: 500;
            text-align: center;
            font-size: 16px;
            margin-bottom: 8px;
            /* space between the text and the number/status */
        }

        span {
            color: #000;
            font-size: 18px;
            font-weight: 600;
        }

        .blue-btn {
            background-color: #004aad;
            /* dark blue */
            color: #ffffff;
            /* white text */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin: 10px 0;
            /* gives space between buttons */
            width: 100%;
            /* makes buttons take the full width of the column */
            transition: background-color 0.3s;
            /* smooth color transition for hover effect */
        }

        .blue-btn:hover {
            background-color: #00367a;
            /* slightly darker blue for hover effect */
        }

        .column.second {
            background-color: transparent;
            /* Clear background */
            box-shadow: none;
            /* Remove shadow */
        }


        .column.third {
            width: 30%;
            max-height: 540px;
            /* Adjust this based on your preference */
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
            color: #000;
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
            @font-face {
                font-family: 'Jameel-Noori-Nastaleeq-Regular';
                src: url({{ public_path('assets/fonts/Jameel-Noori-Nastaleeq-Regular.ttf')}}) format('truetype');
            }

            .urdu-text {
                font-family: 'Jameel-Noori-Nastaleeq-Regular', sans-serif;
            }

            .column.first,
            .column.second,
            .column.third {
                width: 100%;
                margin-bottom: 20px;
                /* Space between columns when stacked */
            }

            .blue-btn {
                width: 48%;
                /* Allow two buttons side by side with a little space */
                margin-right: 4%;
                margin-bottom: 10px;
            }

            .blue-btn:nth-child(even) {
                margin-right: 0;
                /* Reset margin for even buttons */
            }

            .queue-number {
                font-size: 32px;
                color: #000;
                border: 3px solid #000;
                margin: 8px 0;
                /* padding: 10px 20px; */
                border-radius: 10px;
                font-weight: 700;
                width: 90%;
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

       
        
    </style>
    <section id="mainsection">
        <div class="container">
            <!-- main content -->
            <div class="main-content" id="main-target"> 

                <div class="d-flex justify-content-center " style="text-align: center">
                    <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                        <img src="{{ $logoDataUri }}" alt=""> 
                        {{-- <img src="{{ $LogoUrl }}" alt=""> --}} 
                    </a>
                  
                </div>
                <a href="https://kahayfaqeer.org/" target="_blank"><h3>kahayfaqeer.org</h3></a>

                <h2 class="text-center"> Dua Appointment <span class="text-center text-success" style="color:green"> Confirmed
                </span> <br> With <b> Qibla Syed Sarfraz Ahmed Shah Sahab </b>
               </h2>

               


                
                <h3 class="text-center"> </h3>

                <div class="column first">
                    <h2 class="orng">Event Date : {{ \Carbon\Carbon::parse($venueAddress->venue_date)->format('l') }}
                         {{ date('d-M-Y', strtotime($venueAddress->venue_date)) }}</h4>

                        <h2 class="">Venue : {{ $venueAddress->city }} </h2>
                        <div class="venue-info">
                            <h4>{{ $venueAddress->address }}</h4>
                        </div>
                        {{-- <div class="ahead-number">
                        Ahead You #{{ sprintf("%03s", $aheadPeople)  }}
                        </div> --}}
                        <div class="queue-number">
                            Token # {{ $userBooking->booking_number }}
                            <br>
                            {{-- <h3>{{ $userBooking->fname }} {{ $userBooking->lname }}</h3> --}}
                            {{-- <p>{{ $userBooking->email }}</p> --}}
                            <p>{{ $userBooking->country_code }} {{ $userBooking->phone }}</p>
                            <span>Your Appointment Time : </span> <br>
                            <span>{{ date('g:i A', strtotime($userSlot->slot_time)) }} </span>
                            <span>({{ $venueAddress->timezone }})</span>
                        </div>

                        <h3>Appointment Duration</h3>
                        <p>{{ $venueAddress->slot_duration }} {{ ($venueAddress->slot_duration == 1) ? 'minute' : 'minutes' }}  1 Question </p>
                        <div class="stats text-center" style="text-align: center; width:100%">
                          
                            <p class="statement-notes" style="text-align: center;" >{{ $venueAddress->status_page_note }}</p>
                            
                            <p style="text-align: center" >To view your token online please click below:</p>
                            <p style="text-align: center" > <a style="text-align: center" href="{{ route('booking.status', [$userBooking->booking_uniqueid]) }}"
                                    target="_blank">{{ route('booking.status', [$userBooking->booking_uniqueid]) }}</a>
                            </p> 
                        </div>

                </div>
            </div>
        </div> 
    </section>
@endsection 
 