<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
        background-color: #f8f9fa;
        color: #333;
    }

    h1,
    h2,
    h3 {
        text-align: center;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }

    table th,
    table td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: center;
    }

    table th {
        background-color: #b2b9bf;
        color: #202020;
        /* text-transform: uppercase; */
    }

    .summary {
        font-weight: bold;
        margin-top: 10px;
    }

    .section-title {
        font-size: 1em;
        margin: 8px 0 2px;
        text-transform: uppercase;
    }

    .report-meta {
        text-align: center;
        margin-top: 2px;
        font-size: 0.9em;
        color: #555;
    }

    .highlight {
        background-color: #b2b9bf;
        font-weight: bold;
    }

    .head img {
        width: 80px;
        height: 80px;
    }

    .head {
        text-align: center;
    }

    .Timestamp {
        float: right;
        position: absolute;
        right: 31px;
        top: 23px;
        text-align: justify;
    }

    .mt-10 {
        margin-top: 25%;
    }

    .report-meta span {
        font-size: 20px;
    }

    .row-red {
        color: red;
    }

    button#generate-pdf {
        background: black;
        color: #fff;
        padding: 12px;
        border-radius: 50px;
        float: right;
        margin: 10px 0;
        cursor: pointer;
    }

    .tcv {
        display: flex;
        justify-content: space-around;
    }

    .tcv p {
        padding: 4px 40px;
        background: #dedede;
        font-weight: 700;
        border-left: 1px solid;
    }
</style>

<body>
    @php
        $date = request()->get('date') ?? '' ;
        // $date->format('l, d-M-y, h:i A')
    @endphp
    <div class="Timestamp">
        <span>Report Generated at: <br /> {{ \Carbon\Carbon::now()->format('l, d-M-y, h:i A') }}

        </span>
    </div>
    <div class="head">
        <img src="https://app.kahayfaqeer.org/assets/theme/img/logo.png" />

        <div class="report-meta">
            <span><b> DUA / DUM TOKENS SUMMARY REPORT</b></span><br />
            <span><b>{{ (isset($calculations['todayVenue'])) ? strtoupper($calculations['todayVenue']->city) :'' }} DUA GHAR</b></span><br>
            <span><b>    {{ isset($calculations['todayVenue']) ? \Carbon\Carbon::parse($calculations['todayVenue']->venue_date)->format('l, d-M-Y') : '' }}
                </b></span>
        </div>

    </div>

    <button id="generate-pdf">Download PDF</button>

    <!-- QR TOKEN SUMMARY -->
    <div class="section-title"><b>A) QR TOKEN SUMMARY</b></div>
    <table>
        <tr>
            <th>Token Type</th>
            <th>Website Token Registration</th>
            <th>WhatsApp Confirmation Message Sent</th>
            <th>Token Scan Check-in</th>
            <th>Token Print</th>
            <th>Token QR Door Access</th>
        </tr>
        <tr>
            <td>Dua Tokens</td>
            <td>{{$calculations['website-total-dua']}}</td>
            <td>{{$calculations['website-total-wa-dua']}}</td>
            <td>{{$calculations['website-checkIn-dua']}}</td>
            <td>{{$calculations['website-printToken-dua']}}</td>
            <td>{{ $calculations['website-doorAccess-dua'] }}</td>
        </tr>
        <tr>
            <td>Dum Tokens</td>
            <td>{{$calculations['website-total-dum']}}</td>
            <td>{{$calculations['website-total-wa-dum']}}</td>
            <td>{{$calculations['website-checkIn-dum']}}</td>
            <td>{{$calculations['website-printToken-dum']}}</td>
            <td>{{ $calculations['website-doorAccess-dum'] }}</td>
        </tr>
        <tr>
            <td>Working Ladies (Dua)</td>
            <td>{{$calculations['website-total-wldua']}}</td>
            <td>{{$calculations['website-total-wa-wldua']}}</td>
            <td>{{$calculations['website-checkIn-wldua']}}</td>
            <td>{{$calculations['website-printToken-wldua']}}</td>
            <td>{{ $calculations['website-doorAccess-wldua'] }}</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>{{$calculations['grand-total']}}</td>
            <td>{{$calculations['grand-wa']}}</td>
            <td>{{$calculations['grand-checkIn']}}</td>
            <td>{{$calculations['grand-printToken']}}</td>
            <td>{{ array_sum([$calculations['website-doorAccess-dua'], $calculations['website-doorAccess-dum'], $calculations['website-doorAccess-wldua'], $calculations['website-doorAccess-wldum']]) }}</td>

        </tr>
    </table>

    <!-- OUT OF SEQUENCE QR TOKEN DOOR ACCESS -->
    <div class="section-title"><b>B) OUT OF SEQUENCE QR TOKEN DOOR ACCESS</b> </div>
    <table>
        <tr>
            <th>Token Type</th>
            <th>Out of Sequence QR Token Door Access</th>
        </tr>
        <tr>
            <td>Dua Tokens</td>
            <td>{{ $calculations['website-outOfSeq-dua'] }}</td>
        </tr>
        <tr>
            <td>Dum Tokens</td>
            <td>{{ $calculations['website-outOfSeq-dum'] }}</td>
        </tr>
        <tr>
            <td>Working Ladies (Dua)</td>
            <td>{{ $calculations['website-outOfSeq-wldua'] }}</td>
        </tr>
        <tr>
            <td>Working Ladies (Dum)</td>
            <td>{{ $calculations['website-outOfSeq-wldum'] }}</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>{{ array_sum([$calculations['website-outOfSeq-dua'], $calculations['website-outOfSeq-dum'], $calculations['website-outOfSeq-wldua'], $calculations['website-outOfSeq-wldum']]) }}</td>
        </tr>
    </table>

    <!-- STAFF QR DOOR ACCESS -->
    <div class="section-title "><b>C) STAFF QR DOOR ACCESS BETWEEN 2:00 PM TO 5:30 PM</b></div>
    <table>
        <tr>
            <th>Staff Name</th>
            <th>Staff QR Door Access</th>
        </tr>
        @foreach ($calculations['staff-access'] as $staffName => $accessCount)
            <tr>
                <td>{{ $staffName }}</td>
                <td>{{ $accessCount }}</td>
            </tr>
        @endforeach
        <tr class="highlight">
            <td>Total</td>
            <td>{{ $calculations['total-access'] }}</td>
        </tr>
    </table>

    <div class="section-title  page-split "><b>D) ADMIN STAFF DOOR ACCESS LOG</b></div>
    <div class="tcv">
        @foreach ($calculations['staff-total-counts'] as $staffName => $count)
        <p>Total {{ $staffName }} : {{ $count }}</p>
        @endforeach
        {{-- <p>Total Dr Azhar : 3</p>
        <p>Total Waheed : 31</p>
        <p>Total Naseem : 2</p> --}}
    </div>
    <table>
        <tr>
            <th>Door Access Timestamp</th>
            <th>Staff Name</th>
        </tr>
        @foreach ($calculations['staff-access-logs'] as $staffName => $accessLogs)
        @foreach ($accessLogs as $log)
            <tr>
                <td>{{ $log->created_at->format('d-m-Y h:i:s A') }}</td>
                <td>{{ $staffName }}</td>
            </tr>
        @endforeach
    @endforeach
    <tr class="highlight">
        <td>Total</td>
        <td>{{ $calculations['grand-total-access'] }}</td>
    </tr>
    </table>

    <!-- ADMIN STAFF LOG -->
    <div class="section-title page-split1"><b>E) DUA / DUM DOOR ACCESS LOG</b></div>
    <table>
        <tr>
            <th>Door Access Timestamp</th>
            <th>Dua Ghar</th>
            <th>Dua Type</th>
            <th>Token Number</th>
            <th>Phone</th>
            <th>Out of Sequence Access</th>
            <th>Token URL</th>
        </tr>
        @foreach ($calculations['door-logs'] as $log)
        @if(!empty($log) && !empty($log->visitor->booking_number))
        <tr class="{{ $log->out_of_seq == 1 ? 'row-red' : '' }}">
            <td>{{ $log->created_at->format('d-m-Y h:i:s A') }}</td>
            <td>{{ (isset($calculations['todayVenue'])) ? $calculations['todayVenue']->city :'' }}</td> <!-- Assuming venue_address is the field for "Dua Ghar" -->
            <td>{{ ($log->visitor) ?  $log->visitor->dua_type : 'Staff'}}</td>
            <td>{{ ($log->visitor) ? $log->visitor->booking_number : 'N/A' }}</td> <!-- Assuming token_number is stored in visitor -->
            <td>{{ ($log->visitor) ? $log->visitor->phone  : 'N/A'}}</td> <!-- Assuming phone is stored in visitor -->
            <td>{{ ($log && $log->out_of_seq == 1) ? 'Yes' : 'No' }}</td>
            <td><a href="{{ ($log->visitor) ? route('booking.status', $log->visitor->booking_uniqueid) : '#' }}" target="_blank">URL</a></td> <!-- Assuming token_url is stored in visitor -->
        </tr>
        @endif
    @endforeach



    </table>

    {{-- <div class="report-meta">Report Generated at: Sunday, 16-Dec-24, 5:33 PM</div> --}}



    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        document.getElementById("generate-pdf").addEventListener("click", function() {
            const button = document.getElementById("generate-pdf");
            const pageSplitElements = document.querySelectorAll('.page-split');
            button.style.display = "none";
            const element = document.body; // Target the entire body for PDF generation
            pageSplitElements.forEach(element => {
                element.classList.add('mt-10');
            });
            setTimeout(() => {
                const options = {
                    margin: 10,
                    filename: 'QR_Token_Report.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
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
                html2pdf().from(element).set(options).save().then(() => {
                    // Show the button back after PDF generation
                    button.style.display = "inline-block";

                    // Remove the added class from elements
                    pageSplitElements.forEach(element => {
                        element.classList.remove('mt-10');
                    });
                });
                // html2pdf().from(element).set(options).save();
                // button.style.display = "block";
            }, 800);
            // Set PDF options

            // Generate and download PDF


        });
    </script>
