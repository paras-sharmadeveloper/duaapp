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
        <span>Report Generated at: <br />  Sunday, 16-Dec-24, 5:33 PM</span>
    </div>
    <div class="head">
        <img src="https://app.kahayfaqeer.org/assets/theme/img/logo.png" />

        <div class="report-meta">
            <span><b> DUA / DUM TOKENS SUMMARY REPORT</b></span><br />
            <span><b>ISLAMABAD DUA GHAR</b></span><br>
            <span><b>Monday, 16-Dec-24</b></span>
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
            <td>234</td>
        </tr>
        <tr>
            <td>Dum Tokens</td>
            <td>{{$calculations['website-total-dum']}}</td>
            <td>{{$calculations['website-total-wa-dum']}}</td>
            <td>{{$calculations['website-checkIn-dum']}}</td>
            <td>{{$calculations['website-printToken-dum']}}</td>
            <td>66</td>
        </tr>
        <tr>
            <td>Working Ladies (Dua)</td>
            <td>{{$calculations['website-total-wldua']}}</td>
            <td>{{$calculations['website-total-wa-wldua']}}</td>
            <td>{{$calculations['website-checkIn-wldua']}}</td>
            <td>{{$calculations['website-printToken-wldua']}}</td>
            <td>0</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>{{$calculations['grand-total']}}</td>
            <td>{{$calculations['grand-wa']}}</td>
            <td>{{$calculations['grand-checkIn']}}</td>
            <td>{{$calculations['grand-printToken']}}</td>
            <td>300</td>
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
            <td>27</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>27</td>
        </tr>
    </table>

    <!-- STAFF QR DOOR ACCESS -->
    <div class="section-title "><b>C) STAFF QR DOOR ACCESS BETWEEN 2:00 PM TO 5:30 PM</b></div>
    <table>
        <tr>
            <th>Staff Name</th>
            <th>Staff QR Door Access</th>
        </tr>
        <tr>
            <td>Staff Name 1</td>
            <td>23</td>
        </tr>
        <tr>
            <td>Staff Name 2</td>
            <td>12</td>
        </tr>
        <tr>
            <td>Staff Name 3</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Staff Name 4</td>
            <td>6</td>
        </tr>
        <tr>
            <td>Staff Name 5</td>
            <td>17</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>62</td>
        </tr>
    </table>

    <div class="section-title  page-split "><b>D) ADMIN STAFF DOOR ACCESS LOG</b></div>
    <div class="tcv">
        <p>Total Dr Azhar : 3</p>
        <p>Total Waheed : 31</p>
        <p>Total Naseem : 2</p>
    </div>
    <table>
        <tr>
            <th>Door Access Timestamp</th>
            <th>Staff Name</th>
        </tr>
        <tr>
            <td>14-12-2024 6:09:37 AM</td>
            <td>Waheed</td>
        </tr>
        <tr>
            <td>14-12-2024 6:22:40 AM</td>
            <td>Waheed</td>
        </tr>
        <tr class="row-red">
            <td>14-12-2024 6:24:23 AM</td>
            <td>Waheed</td>
        </tr>
        <tr>
            <td>14-12-2024 6:22:40 AM</td>
            <td>Dr Azhar</td>
        </tr>
        <tr>
            <td>14-12-2024 6:22:40 AM</td>
            <td>Dr Azhar</td>
        </tr>
        <tr class="highlight">
            <td>Total</td>
            <td>51</td>
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
        <tr class="row-red">
            <td>14-12-2024 6:09:37 AM</td>
            <td>Islamabad Dua</td>
            <td>Dua</td>
            <td>153</td>
            <td>3343518102</td>
            <td>Yes</td>
            <td><a href="#">URL</a></td>
        </tr>
        <tr>
            <td>14-12-2024 6:22:40 AM</td>
            <td>Islamabad Dua</td>
            <td>Dua</td>
            <td>156</td>
            <td>3347788554</td>
            <td>No</td>
            <td><a href="#">URL</a></td>
        </tr>
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
