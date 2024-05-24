<div id="printableArea">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0px;
            padding: 0px;
        }

        .receipt {
            padding: 3mm;
            width: 70mm;
            border: 1px solid black;
        }
        label.wrkingLady {
    font-size: 22px;
    font-weight: 700;
}

        .orderNo {
            width: 100%;
            text-align: right;
            padding-bottom: 1mm;
            font-size: 8pt;
            font-weight: bold;
        }

        .orderNo:empty {
            display: none;
        }

        .headerSubTitle {
            text-align: center;
            font-size: 12pt;
        }

        .headerTitle {
            text-align: center;
            font-size: 24pt;
            font-weight: bold;
        }

        #location {
            margin-top: 5pt;
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
        }

        #date {
            margin: 5pt 0px;
            text-align: center;
            font-size: 16pt;
        }

        #barcode {
            display: block;
            margin: 0px auto;
        }

        #barcode:empty {
            display: none;
        }

        .watermark {
            position: absolute;
            left: 10mm;
            top: 43mm;
            width: 54mm;
            opacity: 0.1;
            display: none;
        }

        .keepIt {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
        }

        .keepItBody {
            text-align: justify;
            font-size: 8pt;
        }

        .item {
            margin-bottom: 1mm;
        }

        .itemRow {
            display: flex;
            font-size: 8pt;
            align-items: baseline;
        }

        .itemRow>div {
            align-items: baseline;
        }

        .itemName {
            font-weight: bold;
        }

        .itemPrice {
            text-align: right;
            flex-grow: 1;
        }

        .itemColor {
            width: 10px;
            height: 100%;
            background: yellow;
            margin: 0px 2px;
            padding: 0px;
        }

        .itemColor:before {
            content: "\00a0";
        }

        .itemData2 {
            text-align: right;
            flex-grow: 1;
        }

        .itemData3 {
            width: 15mm;
            text-align: right;
        }

        .itemQuantity:before {
            content: "x";
        }

        .itemTaxable:after {
            content: " T";
        }

        .flex {
            display: flex;
            justify-content: center;
        }

        #qrcode {
            align-self: center;
            flex: 0 0 100px
        }

        .totals {
            flex-grow: 1;
            align-self: center;
            font-size: 8pt;
        }

        .totals .row {
            display: flex;
            text-align: right;
        }

        .totals .section {
            padding-top: 2mm;
        }

        .totalRow>div,
        .total>div {
            text-align: right;
            align-items: baseline;
            font-size: 8pt;
        }

        .totals .col1 {
            text-align: right;
            flex-grow: 1;
        }

        .totals .col2 {
            width: 22mm;
        }

        .totals .col2:empty {
            display: none;
        }

        .totals .col3 {
            width: 15mm;
        }

        .footer {
            overflow: hidden;
            margin-top: 5mm;
            border-radius: 7px;
            width: 100%;
            background: black;
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer:empty {
            display: none;
        }

        .eta {
            padding: 1mm 0px;
        }

        .eta:empty {
            display: none;
        }

        .eta:before {
            content: "Estimated time order will be ready: ";
            font-size: 8pt;
            display: block;
        }

        .etaLabel {
            font-size: 8pt;
        }

        .printType {
            padding: 1mm 0px;
            width: 100%;
            background: grey;
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .about {
            font-size: 12pt;
            overflow: hidden;
            background: #FCEC52;
            color: #3A5743;
            border-radius: 7px;
            padding: 0px;
            position: absolute;
            width: 500px;
            text-align: center;
            left: 50%;
            margin-top: 50px;
            margin-left: -250px;
        }

        .arrow_box h3,
        ul {
            margin: 5px;
        }

        .about li {
            text-align: left;
        }

        .arrow_box {
            position: absolute;
            background: #88b7d5;
            padding: 5px;
            margin-top: 20px;
            left: 95mm;
            top: 2;
            width: 500px;
            border: 4px solid #c2e1f5;
        }

        .arrow_box:after,
        .arrow_box:before {
            right: 100%;
            top: 50%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .arrow_box:after {
            border-color: rgba(136, 183, 213, 0);
            border-right-color: #88b7d5;
            border-width: 30px;
            margin-top: -30px;
        }

        .arrow_box:before {
            border-color: rgba(194, 225, 245, 0);
            border-right-color: #c2e1f5;
            border-width: 36px;
            margin-top: -36px;
        }

        .headerr.d-flex.justify-content-around {
            display: flex;
            justify-content: space-between;
        }

        #readAll {
            font-size: 15px;
            text-align: center
        }

        #token {
            margin-top: 5pt;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
        }

        .text-green {
            color: green;
        }

        .text-red {
            color: red;
        }

        .userImag {
            padding: 10px;
        }

        .main-print-di {
            display: flex;
            justify-content: space-evenly;
        }

        .link_wrap {
            position: relative;
            margin-bottom: -50px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            line-height: 1;
        }

        .acc_style04 {
            background: #9F1783;
            padding: 1em 4em;
            color: #fff;
            transition: all 0.2s ease-in;
            position: relative;
        }

        .acc_style04::before {
            content: "";
            position: absolute;
            left: 14px;
            top: -2px;
            width: 24px;
            height: 74%;
            background: #c7c7c7;
            clip-path: polygon(100% 0%, 100% 100%, 50% 75%, 0 100%, 0 0);
            transition: all 0.2s ease-in;
        }

        .acc_style04:hover::before {
            height: 90%;
        }
    </style>
    <div class="mycont d-flex justify-content-between">
        <div class="row">


            <div class="col-lg-6">
                <div class="receipt" id="printONl">
                    <img class="watermark" src="https://app.kahayfaqeer.org/assets/theme/img/logo.png">
                    <div class="headerr d-flex justify-content-around">
                        @php
                            $bookstatus = route('booking.status', [$visitor->booking_uniqueid]);
                        @endphp

                        <div class="qr">
                            {!! QrCode::size(90)->generate($bookstatus) !!}
                        </div>
                        <div class="logod">
                            <img style="height: 60px !important; width:60px !important"
                                src="https://app.kahayfaqeer.org/assets/theme/img/logo.png">
                        </div>
                        <div class="qr">
                            {!! QrCode::size(90)->generate($bookstatus) !!}
                        </div>

                    </div>
                    {{-- <div class="orderNo">
                            Token ID# <span id="Order #">71</span>: <span id="Order Name">Dua</span>
                        </div> --}}
                    <div class="headerSubTitle mt-3">
                        <p>
                            <b
                                class="{{ date('Y-m-d', strtotime($visitor->slot->venueAddress->venue_date)) == date('Y-m-d') ? 'text-green' : 'text-red' }}">
                                Date: {{ date('l d M Y', strtotime($visitor->slot->venueAddress->venue_date)) }}
                            </b>
                        </p>
                        {{-- <p> <b>Date : {{ date('l d M Y',strtotime($visitor->slot->venueAddress->venue_date)) }} </b> </p> --}}
                        <span> Venue : {{ $visitor->slot->venueAddress->city }} Dua Ghar </span>
                    </div>


                    <div id="token">
                        {{  ucwords(str_replace("_"," ",$visitor->slot->type)) }} Token # {{ $visitor->slot->token_id }}
                    </div>

                    <div id="date">
                        {{ $visitor->country_code }} {{ $visitor->phone }}
                    </div>
                    <div id="location">
                        TOKEN VERIFIED <span class="checkmark"></span>
                    </div>
                    <svg id="barcode"></svg>

                    <hr>

                    <div id="readAll">
                        Read / listen all books for free
                    </div>
                    <div class="headerSubTitle">
                        <b> KahayFaqeer.org </b>
                    </div>

                </div>
            </div>


        </div>

        <div class="row userImag" >
            <div class="link_wrap">
                <label class="wrkingLady"> {{ ($workingLady) ? 'Working Lady' : 'Normal Person' }}</label>
                @if(!empty($workingLady) && $workingLady->type == 'critical' )
                    <a class="acc_style04" href="#" style="background:red">
                        {{ ($workingLady) ? ucwords($workingLady->type)  : '' }}
                    </a>
                @endif
                @if(!empty($workingLady) && $workingLady->type == 'normal' )
                    <a class="acc_style04" href="#" style="background:rgb(70, 148, 70)">
                        {{ ($workingLady) ? ucwords($workingLady->type) : '' }}
                    </a>
                @endif
            </div>
            <div class="col-lg-6">
                <label class="fw-bold"> Token Session Image </label>
                @if($UserImage)
                <img src="data:image/jpeg;base64,{{ base64_encode($UserImage) }}" alt="Preview Image"
                    style="height: 150px; width:150px;border-radius:20%">
                @else
                <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png" alt="Preview Image"
                style="height: 150px; width:150px;border-radius:20%">
                @endif
            </div>
            <div class="col-lg-6">
                <label class="fw-bold"> Database Image</label>
                @if($databaseImage)
                <img src="data:image/jpeg;base64,{{ base64_encode($databaseImage) }}" alt="Preview Image"
                style="height: 150px; width:150px;border-radius:20%">
                @else
                <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png" alt="Preview Image"
                style="height: 150px; width:150px;border-radius:20%">
                @endif
            </div>
        </div>

    </div>

</div>
