@extends('layouts.app')

@section('content')
    <style>
        .token-area {
            text-align: center;
            margin: 20px;
        }

        button#html5-qrcode-button-camera-stop {
            padding: 10px;
            border: white;
        }

        div#model-body {
            text-align: center;
            display: flex;
            justify-content: center;
            height: 350px;
            overflow: auto;
        }

        #footer {
            display: none;
        }

        .modal-header {
            display: flex;
            justify-content: center;
        }

        .custom-loader {
            width: 48px;
            height: 48px;
            border: 3px solid #000;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .custom-loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-bottom-color: #58c05c;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <input id='barcodeInput' type='text' class="form-control w-100" />
                <div class="tickmark mt-2" id="greenTick" style="display: none">
                    <img width="50" height="50" src="https://img.icons8.com/ios-filled/50/40C057/checked--v1.png"
                        alt="checked--v1" />
                    <p>You can start scaning</p>
                    <span class="loader custom-loader" style="display: none"></span>
                </div>

            </div>

        </div>

    </div>






    <div class="modal fade bd-example-modal-lg" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="alert alert-danger" id="invaild-token" style="display: none"></div>
                    <div class="alert alert-success" id="vaild-token" style="display: none"></div>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="model-body">
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="close btn btn-warning">Close</button>
                    <button type="button" onclick="printDiv('printableArea')" class="btn btn-dark printDiv">Print </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#barcodeInput").focus();
        $("#greenTick").show()
        var barcodeValue = '';
        // document.getElementById('barcodeInput').addEventListener('input', function(event) {
        //     barcodeValue = event.target.value;
        // });
        // console.log("Scanned Barcode:", barcodeValue);

        document.getElementById('barcodeInput').addEventListener('change', function(event) {
            barcodeValue = event.target.value;
            $(".custom-loader").show();

            $.ajax({
                url: "{{ route('process-scan') }}",
                method: 'POST',
                data: {
                    id: barcodeValue,
                    type: 'verify'
                },
                success: function(response) {

                    $(".custom-loader").hide();
                    // Handle success
                    if (response.success) {

                        //   alert("if here")
                        $(".token-area").find('p').show();
                        $(".token-area").find('span').text(response.token)

                        $("#vaild-token").text(response.message).show();
                        $("#invaild-token").hide();
                        $("#model-body").html(response.printToken)
                        $('#myModal').modal('toggle');

                    } else {
                        //   alert("else here")
                        $(".token-area").find('p').hide();
                        $('#myModal').modal('toggle');
                        $("#model-body").html(response.printToken)

                        if (!response.print) {
                            $("#vaild-token").hide();
                            $("#invaild-token").text(response.message).show();
                        }
                        // toastr.error('There Might be some issue at backend');

                    }

                },
                error: function(error) {
                    // Handle error
                    // toastr.error('Error: Unable to process the scan.');
                }
            });
            $("#barcodeInput").val('');


        });


        function printDiv(divId) {
            $(".userImag").hide()

            //  alert("in print code here")
            printCount()
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');

            printWindow.document.close(); // necessary for IE >= 10
            printWindow.onload = function() {
                // alert("in print code printing")
                printWindow.print();
                printWindow.close();
            };
            $("#barcodeInput").focus();
            $('#myModal').modal('hide');


        }


        $(document).on("click", ".close", function() {
            $('#myModal').modal('hide');
            $("#barcodeInput").focus();
        })


        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {
                $('#myModal').modal('hide');
                $("#barcodeInput").focus();


            }
        });

        $(document).keydown(function(event) {

            if (event.which == 32) {
                // alert("space pressed")
                $(".userImag").remove()
                printDiv('printableArea')
                // $("#barcodeInput").focus();
            }
        });

        function printCount() {
            var visitorId = $("#visitorIdPopUp").attr('data-id');
            $.ajax({
                url: "{{ route('count-print-count') }}",
                method: 'POST',
                data: {
                    id: visitorId,
                },
                success: function(response) {


                },
                error: function(error) {

                }
            });

        }
    </script>
@endsection
