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
        }

        #footer {
            display: none;
        }

        .modal-header {
            display: flex;
            justify-content: center;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <input id='barcodeInput' type='text'  class="form-control w-100"  />
                <div class="tickmark">
                    <img width="50" height="50" src="https://img.icons8.com/ios-filled/50/40C057/checked--v1.png" alt="checked--v1" id="greenTick" style="display: none"/>
                    <p>You can start scaning</p>
                </div>



            </div>
        </div>


    </div>


    <div class="modal" id="myModal">
        <div class="modal-dialog">
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
                    <button type="button" onclick="printDiv('printableArea')" class="btn btn-dark ">Print </button>
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
        var barcodeValue ='';
        // document.getElementById('barcodeInput').addEventListener('input', function(event) {
        //     barcodeValue = event.target.value;
        // });
        // console.log("Scanned Barcode:", barcodeValue);

        document.getElementById('barcodeInput').addEventListener('change', function(event) {
            barcodeValue = event.target.value;

            $.ajax({
                url: "{{ route('process-scan') }}",
                method: 'POST',
                data: {
                    id: barcodeValue,
                    type: 'verify'
                },
                success: function(response) {
                    // Handle success
                    if (response.success) {
                        $(".token-area").find('p').show();
                        $(".token-area").find('span').text(response.token)

                        $('#myModal').modal('toggle');
                        $("#vaild-token").text(response.message).show();
                        $("#invaild-token").hide();
                        $("#model-body").html(response.printToken)

                        // toastr.success(response.message);
                        html5QrcodeScanner.resume();
                    } else {
                        $(".token-area").find('p').hide();
                        $('#myModal').modal('toggle');
                        $("#model-body").html(response.printToken)
                        if (!response.print) {
                            // $("#printButton").hide();
                            $("#vaild-token").hide();
                            $("#invaild-token").text(response.message).show();
                        }

                    }

                },
                error: function(error) {
                    // Handle error
                    toastr.error('Error: Unable to process the scan.');
                }
            });
            $("#barcodeInput").val('');


            console.log("Scanned Barcode:", barcodeValue);
        });


        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');

            printWindow.document.close(); // necessary for IE >= 10
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        }

        $(document).on("click", ".close", function() {
            $('#myModal').modal('hide');
        })





    </script>
@endsection
