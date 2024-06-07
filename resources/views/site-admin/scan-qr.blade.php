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
            height: 380px;
            overflow: auto;
        }

        #footer {
            display: none;
        }

        .modal-header {
            display: flex;
            justify-content: center;
            padding: 4px;
        }

        .alert {
            padding: 10px;
            text-align: left;

        }

    </style>

@if(request()->get('showUserImage') != 'true')

<style>
    /* .userImag{
        display: none;
    } */
    </style>
@endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <a href="{{ route('qr.gun.scan') }}" class="btn btn-success">Scan With Gun</a>
                @if (request()->get('show') == null || request()->get('show') == 'false')
                    <a href="{{ route('qr.show.scan') }}?show=true" class="btn btn-success">Scan With Camera</a>
                @endif
            </div>

        </div>
        <div class="row justify-content-center mt-5">
            @if (request()->get('show') == 'true')
                <div class="col-md-12">
                    <div class="embed-responsive embed-responsive-1by1" id="reader"></div>
                </div>
            @endif
        </div>
        <div class="token-area">
            <p style="display: none"><b>Token Number is: </b></p> <span></span>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var scannerPaused = false;

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 1,
                qrbox: 350,
                legacyMode: true
            });

        function onScanSuccess(decodedText, decodedResult) {

            html5QrcodeScanner.pause(); // Pause scanner

            $.ajax({
                url: "{{ route('process-scan') }}",
                method: 'POST',
                data: {
                    id: decodedText,
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
                        // setTimeout(() => {
                        //     printDiv('printableArea')
                        // }, 1500);
                        // toastr.success(response.message);
                        html5QrcodeScanner.pause();
                    } else {

                        $(".token-area").find('p').hide();
                        $('#myModal').modal('toggle');
                        $("#model-body").html(response.printToken)

                        if (!response.print) {
                            // $("#printButton").hide();
                            $("#vaild-token").hide();
                            $("#invaild-token").text(response.message).show();


                        }
                        // setTimeout(() => {
                        //     printDiv('printableArea')
                        // }, 1500);


                        html5QrcodeScanner.resume();
                    }

                },
                error: function(error) {
                    html5QrcodeScanner.resume();
                    // Handle error
                    toastr.error('Error: Unable to process the scan.');
                }
            });

            $(document).on("click", ".close", function() {
                $('#myModal').modal('hide');
                html5QrcodeScanner.resume();
            })

        }
        html5QrcodeScanner.render(onScanSuccess);



        $(document).ready(function() {
            $("#html5-qrcode-button-camera-stop").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-permission").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-start").addClass('btn btn-info');

        })

        function printDiv(divId) {
            printCount()
            $(".userImag").hide()

            $(".main-print-di").css('display', 'block');
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
            $('#myModal').modal('hide');

            html5QrcodeScanner.resume();
        }

        document.addEventListener('DOMContentLoaded', function() {

            const target = document.getElementById('myModal');

            document.addEventListener('click', function(event) {

                html5QrcodeScanner.resume();
                // Check if the click occurred inside or outside the target element
                if (event.target === target) {
                    console.log('Clicked inside the target element.');
                } else {
                    console.log('Clicked outside the target element.');
                }
            });
        });

        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {
                $('#myModal').modal('hide');
                html5QrcodeScanner.resume();
            }
        });



        $(document).keydown(function(event) {
            // Check if the pressed key is Enter (key code 13)
            // Check if the pressed key is Space (key code 32)
            if (event.which == 32) {
                $(".userImag").remove()
                printDiv('printableArea')

            }
        });

        function printCount(){
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
