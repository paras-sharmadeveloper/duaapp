@extends('layouts.guest')
@section('content')
    <section id="main-content">
        <div class="container">
            <!-- main content -->
            <div class="main-content">

                <div class="d-flex justify-content-center py-2">
                    <a href="index.html" class="logoo  d-flex align-items-center wuto">
                        <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">
                        <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
                    </a>
                </div>
               

                <h2 class="text-center"> Dua <span class="text-center text-success h2"> Confirmed </span>  With 
                    <b> {{ $venueAddress->thripist->name }}  </b>
                </h2>
                <h3 class="text-center"> </h3>
                
                <div class="column first">
                    <h2 class="orng">Event Date : {{ date('d-M-Y', strtotime($venueAddress->venue_date)) }}</h4>

                        <h2 class="">Venue : {{ $venueAddress->city }} </h2>
                        <div class="venue-info">
                            <h6>{{ $venueAddress->address }}</h6>
                        </div>
                        {{-- <div class="ahead-number">
                        Ahead You #{{ sprintf("%03s", $aheadPeople)  }}
                        </div> --}}
                        <div class="queue-number">
                            Token #{{ $userBooking->booking_number }}
                            <br>
                            {{-- <h3>{{ $userBooking->fname }} {{ $userBooking->lname }}</h3> --}}
                            {{-- <p>{{ $userBooking->email }}</p> --}}
                            <p>{{ $userBooking->country_code }} {{ $userBooking->phone }}</p>
                            <span>Your Appointment Time : </span> <br>
                            <span>{{ date('g:i A', strtotime($userSlot->slot_time)) }} </span>
                            <span>({{ $venueAddress->timezone }})</span>
                        </div>

                        <h3>Appointment Duration</h3>
                        <p>{{ $venueAddress->slot_duration }} minutes 1 Question </p>
                        <div class="stats text-center">
                            <p>{{  $venueAddress->status_page_note }}</p>

                            <button type="button" class="btn btn-success download-apponit" onclick="downloadPdf()">Download
                                Appointment</button>

                        </div>

                </div>
            </div>
        </div>

    </section>
    <style type="text/css">
        @media print {
            .hide-in-print {
                display: none;
            }
        }

        @media print {

            /* Adjust widths for better print layout */
            .column {
                width: 100%;
            }

            .element-with-box-shadow {
                box-shadow: none;
                /* Remove box shadow */
            }

            .element-with-transparency {
                opacity: 1;
                /* Set full opacity */
            }

            body,
            html {
                background-color: #fff;
                /* Set to your preferred background color */
            }

            /* Add additional styles for better print layout */
        }

        .venue-info h6,
        .stats h3 {
            color: #000;
        }

        .queue-number span {
            font-size: 14px;
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
            padding: 50px 20px;
            border-radius: 10px;
            font-weight: 700;
            width: 50%;
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
            .container {
                flex-direction: column;
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.title = "KahayFaqeer.com | Queue Status";
    var fileName = "{{  $venueAddress->venue_date .'-'. $venueAddress->city .'-Token'.$userBooking->booking_number  }}"
    window.setTimeout(function() {
        window.location.reload();
    }, 30000);

    function downloadPdf() {
 
        $(".download-apponit").hide(); 
        const element = document.getElementById('main-content');
        const formattedDate = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
        const options = {
            margin: 0,
            filename: fileName + '.pdf',
            image: {
                type: 'jpeg',
                quality: 1.0
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };
        html2pdf(element, options);
        // $(".download-apponit").show();  
    }
</script>
