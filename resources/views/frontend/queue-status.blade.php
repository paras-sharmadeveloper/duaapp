@extends('layouts.guest')
@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Jameel Noori Nastaleeq', 'Regular';
        }

        @media print {

            /* Adjust widths for better print layout */
            .column {
                width: 100%;
                margin: 0;
                padding: 10px;
                /* Adjust padding as needed */
            }

            #mainsection {
                margin: 0 !important;
            }


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
            font-weight: 700 !important;
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

        .enlarged-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* semi-transparent black overlay */
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* ensure the overlay is on top of other elements */
        }

        .enlarged-image img {
            max-width: 90%;
            max-height: 90%;
            display: block;
        }

        /* Tablet: Stacking columns vertically */
        @media only screen and (max-width: 992px) {
            .stats {
                text-align: center !important;
            }

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

        span.text-center.text-success {
            font-size: 24px;
        }

    </style>
    <section id="mainsection">
        <div class="container">
            <!-- main content -->
            <div class="main-content" id="main-target">

                <div class="d-flex justify-content-center ">
                    <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                        <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">

                    </a>

                </div>
                <a href="https://kahayfaqeer.org/" target="_blank">
                    <h3>kahayfaqeer.org</h3>
                </a>


                <h2 class="text-center">{{ trans('messages.pdf_title_1') }} <span class="text-center text-success">
                        {{ trans('messages.pdf_title_confirm') }}
                        {{-- </span class="h2"> <br> With <b> {{ $venueAddress->thripist->name }} </b> --}}
                    </span class="h2"> <br> <b> {{ trans('messages.pdf_title_confirm_with') }} </b>
                </h2>
                <h3 class="text-center"> </h3>

                <div class="column first">
                    @php
                        $day = \Carbon\Carbon::parse($venueAddress->venue_date)->format('l');
                        $transofWeekDays = trans('messages.Week_day_' . $day);
                        $city =  trans('messages.' . $venueAddress->city);
                    @endphp
                     @if(empty($userBooking->lang) || $userBooking->lang == 'en')
                        <h2 class="orng">{{ trans('messages.pdf_event_date_label') }} : {{ $transofWeekDays }}
                             {{ date('d-M-Y', strtotime($venueAddress->venue_date)) }}
                        </h4>
                        @else
                        <h2 class="orng">
                             <span>{{ trans('messages.pdf_event_date_label') }}</span>  :  {{ $transofWeekDays }} {{ date('Y-m-d', strtotime($venueAddress->venue_date)) }}
                        </h4>
                        @endif

                        <h2 class="">{{ trans('messages.pdf_event_venue_label') }} : {{ $city }} </h2>
                        <div class="venue-info">
                        @if(empty($userBooking->lang) || $userBooking->lang == 'en')
                            <h4>{{ $venueAddress->address }}</h4>
                        @else

                         <h4>{{ $venueAddress->address_ur }}</h4>

                        @endif
                        </div>
                        {{-- <div class="ahead-number">
                        Ahead You #{{ sprintf("%03s", $aheadPeople)  }}
                        </div> --}}
                        <div class="queue-number">
                            {{ ucwords(trans('messages.'.$slotType))  }} {{ trans('messages.pdf_event_token_label') }}  # {{ $userBooking->booking_number }}
                            <br>
                            <p>{{ $userBooking->country_code }} {{ $userBooking->phone }}</p>
                        </div>

                        <div class="queue-qr-scan">

                          {!! QrCode::size(190)->generate($userBooking->booking_uniqueid) !!}

                        </div>



                        <h3>{{ trans('messages.pdf_event_token_appointment_lable') }}</h3>
                        <p>{{ $venueAddress->slot_duration }} {{ trans('messages.pdf_event_token_mint') }} 1
                            {{ trans('messages.pdf_event_token_question') }} </p>
                        <div class="stats text-center">
                          @if(empty($userBooking->lang) || $userBooking->lang == 'en')
                            <p class="statement-notes">{{ $venueAddress->status_page_note }}</p>
                            @else
                            <p class="statement-notes">{{ $venueAddress->status_page_note_ur }}</p>

                            @endif
                            <p>{{ trans('messages.pdf_event_token_view_label') }}:</p>
                            <p> <a href="{{ route('booking.status', [$userBooking->booking_uniqueid]) }}"
                                    target="_blank">{{ route('booking.status', [$userBooking->booking_uniqueid]) }}</a>
                            </p>

                            {{-- <a href="{{ route('generate-pdf',[$userBooking->booking_uniqueid]) }}" class="btn btn-success" >{{ trans('messages.pdf_download_btn_label') }}</a> --}}
                            {{-- <a href="{{ route('generate-pdf', [$userBooking->booking_uniqueid]) }}"
                                class="btn btn-success">{{ trans('messages.pdf_download_btn_label') }}</a> --}}
                            <button type="button" class="btn btn-success download-apponit" id="cmd"
                                onclick="downloadPdf()">{{ trans('messages.pdf_download_btn_label') }}</button>

                        </div>

                </div>
            </div>
        </div>

    </section>
@endsection

<!-- Include jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.3/jspdf.umd.min.js"></script> --}}



@section('page-script')
    <script>
        document.title = "KahayFaqeer.com | Queue Status";
        var fileName =
            "{{ $venueAddress->venue_date . '-' . $venueAddress->city . '-Token' . $userBooking->booking_number }}"



        function downloadPdf() {

            $(".download-apponit").hide();
            const element = document.getElementById('main-target');
            const formattedDate = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
            const options = {
                margin: 0,
                format: 'a4',
                filename: fileName + '.pdf',
                // image: {
                //     type: 'jpeg',
                //     quality: 1.0
                // },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };
         //   if (window.innerWidth < 768) {
                // Adjust options for mobile view
            //    options.html2canvas.width = 1000; // Set the desired width for mobile view
             //   options.html2canvas.height = 1200; // Set the desired height for mobile view
           // }
            html2pdf(element, options);
            //  $(".download-apponit").hide();
        }

        // JavaScript to handle image click and enlarge
    </script>
@endsection
