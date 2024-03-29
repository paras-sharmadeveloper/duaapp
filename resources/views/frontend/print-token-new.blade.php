
<style>
    body {font-family: 'Roboto', sans-serif;margin: 0px;padding: 0px;}.receipt {padding: 3mm;width: 70mm;border: 1px solid black;}.orderNo {width: 100%;text-align: right;padding-bottom: 1mm;font-size: 8pt;font-weight: bold;}.orderNo:empty {display: none;}.headerSubTitle {text-align: center;font-size: 12pt;}.headerTitle {text-align: center;font-size: 24pt;font-weight: bold;}#location {margin-top: 5pt;text-align: center;font-size: 16pt;font-weight: bold;}#date {margin: 5pt 0px;text-align: center;font-size: 8pt;}#barcode {display: block;margin: 0px auto;}#barcode:empty {display: none;}.watermark {position: absolute;left: 8mm;top: 20mm;width: 54mm;opacity: 0.1;}.keepIt {text-align: center;font-size: 12pt;font-weight: bold;}.keepItBody {text-align: justify;font-size: 8pt;}.item {margin-bottom: 1mm;}.itemRow {display: flex;font-size: 8pt;align-items: baseline;}.itemRow>div {align-items: baseline;}.itemName {font-weight: bold;}.itemPrice {text-align: right;flex-grow: 1;}.itemColor {width: 10px;height: 100%;background: yellow;margin: 0px 2px;padding: 0px;}.itemColor:before {content: "\00a0";}.itemData2 {text-align: right;flex-grow: 1;}.itemData3 {width: 15mm;text-align: right;}.itemQuantity:before {content: "x";}.itemTaxable:after {content: " T";}.flex {display: flex;justify-content: center;}#qrcode {align-self: center;flex: 0 0 100px }.totals {flex-grow: 1;align-self: center;font-size: 8pt;}.totals .row {display: flex;text-align: right;}.totals .section {padding-top: 2mm;}.totalRow>div, .total>div {text-align: right;align-items: baseline;font-size: 8pt;}.totals .col1 {text-align: right;flex-grow: 1;}.totals .col2 {width: 22mm;}.totals .col2:empty {display: none;}.totals .col3 {width: 15mm;}.footer {overflow: hidden;margin-top: 5mm;border-radius: 7px;width: 100%;background: black;color: white;text-align: center;font-weight: bold;text-transform: uppercase;}.footer:empty {display: none;}.eta {padding: 1mm 0px;}.eta:empty {display: none;}.eta:before {content: "Estimated time order will be ready: ";font-size: 8pt;display: block;}.etaLabel {font-size: 8pt;}.printType {padding: 1mm 0px;width: 100%;background: grey;color: white;text-align: center;font-weight: bold;text-transform: uppercase;}.about {font-size: 12pt;overflow: hidden;background: #FCEC52;color: #3A5743;border-radius: 7px;padding: 0px;position: absolute;width: 500px;text-align: center;left: 50%;margin-top: 50px;margin-left: -250px;}.arrow_box h3, ul {margin: 5px;}.about li {text-align: left;}.arrow_box {position: absolute;background: #88b7d5;padding: 5px;margin-top: 20px;left: 95mm;top: 2;width: 500px;border: 4px solid #c2e1f5;}.arrow_box:after, .arrow_box:before {right: 100%;top: 50%;border: solid transparent;content: " ";height: 0;width: 0;position: absolute;pointer-events: none;}.arrow_box:after {border-color: rgba(136, 183, 213, 0);border-right-color: #88b7d5;border-width: 30px;margin-top: -30px;}.arrow_box:before {border-color: rgba(194, 225, 245, 0);border-right-color: #c2e1f5;border-width: 36px;margin-top: -36px;}.headerr.d-flex.justify-content-around {display: flex;justify-content: space-between;}#readAll{font-size: 15px;text-align: center }#token{margin-top: 5pt;text-align: center;font-size: 22pt;font-weight: bold;}
   </style>
   <!-- START RECEIPT -->
   <div class="receipt" id="printableArea">
       <img class="watermark" src="https://app.kahayfaqeer.org/assets/theme/img/logo.png">
       <div class="headerr d-flex justify-content-around">
           @php
               $bookstatus = route('book.show',['asdioaoisd-1lkjasdklajsd-askdjbnajskdb-1jkaklsjbdnklahns']);
           @endphp

           <div class="qr">
               {!! QrCode::size(60)->generate( $bookstatus  ) !!}
           </div>
           <div class="logo">
               <img style="height: 70px; width:70px" src="https://app.kahayfaqeer.org/assets/theme/img/logo.png">
           </div>
           <div class="qr">
               {!! QrCode::size(60)->generate( $bookstatus  ) !!}
           </div>

       </div>
       {{-- <div class="orderNo">
           Token ID# <span id="Order #">71</span>: <span id="Order Name">Dua</span>
       </div> --}}
       <div class="headerSubTitle">
          <p> <b>Date : Monday 08 Feb 2024</b> </p>
          <span> Venue : Lahore Dua Ghar </span>
       </div>


       <div id="token">
           Dum Token #1
       </div>

       <div id="date">
           +91 8989895050
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
<!-- HTML content -->

<script>
    // Wait for the page to fully load
    window.onload = function() {
        // Call the print function
        window.print();
    };
</script>
