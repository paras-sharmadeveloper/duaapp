@extends('layouts.guest')
@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        h2,h6{color:#000;text-align:center}h2,h3{margin-top:10px}body{font-family:'Jameel Noori Nastaleeq',Regular}.queue-number p{font-size:24px;font-weight:600}@media print{.column{width:100%;margin:0;padding:10px}#mainsection{margin:0!important}}span.text-center.text-success,span.text-center.text-success.confirm{font-size:24px}.orng,.stats h3,.venue-info h6{color:#000}.queue-number span{font-size:20px;color:#000}.column{box-sizing:border-box;padding:20px;background-color:#fff;border-radius:15px;box-shadow:0 4px 10px rgba(0,0,0,.1)}.first{width:100%;display:flex;flex-direction:column;align-items:center}.ahead-number,.queue-number{margin:20px 0;font-weight:700;width:50%}h6{font-size:14px}h2{font-weight:700!important;font-size:24px}.ahead-number,h3{font-size:20px;text-align:center;color:#000}.ahead-number{border:3px solid #000;padding:5px 4px;border-radius:10px}.queue-number{font-size:34px;color:#000;border:3px solid #000;padding:30px 10px;border-radius:10px;text-align:center}p{text-align:center;font-weight:400;font-size:18px;color:#000}.stats{border-radius:10px;padding:10px;width:80%;margin-top:20px}.stat-item{display:flex;flex-direction:column;align-items:center;flex:1;padding:10px;border-radius:8px}h4{color:#000;font-weight:500;text-align:center;font-size:16px;margin-bottom:8px}.blue-btn,span{font-size:18px}span{color:#000;font-weight:600}.blue-btn{background-color:#004aad;color:#fff;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;margin:10px 0;width:100%;transition:background-color .3s}.blue-btn:hover{background-color:#00367a}.column.second{background-color:transparent;box-shadow:none;width:30%}.column.third{width:30%;max-height:540px;overflow-y:auto;background-color:#fff;box-shadow:0 4px 10px rgba(0,0,0,.1);padding:20px}.visitor-list{list-style-type:none;padding:0;margin:0}.visitor-item{border-bottom:1px solid #e0e0e0;padding:10px 0}.visitor-item h4,.visitor-item p{color:#000;margin-bottom:5px}.booking-details{display:flex;justify-content:space-between;align-items:center}.booking-id{color:orange}.slot-time{color:#d3d3d3;display:flex;align-items:center}.slot-time i{margin-right:5px}.enlarged-image{position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,.7);display:none;justify-content:center;align-items:center;z-index:9999}.enlarged-image img{max-width:90%;max-height:90%;display:block}@media only screen and (max-width:992px){.stats{text-align:center!important}.ahead-number,.queue-number{text-align:center;width:100%}.column.first,.column.second,.column.third{width:100%;margin-bottom:20px}.blue-btn{width:48%;margin-right:4%;margin-bottom:10px}.blue-btn:nth-child(2n){margin-right:0}.queue-number{font-size:32px;color:#000;border:3px solid #000;margin:8px 0;padding:10px 20px;border-radius:10px;font-weight:700}.ahead-number{font-size:21px;color:#1900ff;border:3px solid #0048ff;margin:20px 0;padding:5px 4px;border-radius:10px;font-weight:700}.container{flex-direction:column;display:flex;width:100%;padding:4px}h1.text-center{text-align:center;font-size:23px}.queue-number span{font-size:20px;color:#000}}@media only screen and (max-width:768px){.blue-btn{width:100%;margin-right:0;margin-bottom:10px}.logoo img{height:100px;width:100px}}
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

                        <h2 class="">{{ trans('messages.pdf_event_venue_label') }} : {{ $city }} {{ trans('messages.location-dua-ghar') }} </h2>
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
                            <p> {{ ($slotType == 'working_lady_dua' || $slotType == 'working_lady_dum' ) ? 'Working Lady' : '' }} </p>

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
                            @php
                                $userId =  base64_encode($userBooking->id);
                            @endphp
                            <p>
                                <a href="{{ route('booking.status', [$userBooking->booking_uniqueid]) }}"
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
        document.title = "kahayFaqeer.org | Queue Status";
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
