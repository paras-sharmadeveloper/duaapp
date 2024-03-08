@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-1by1" id="reader"></div>
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
                qrbox: 200,
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
                            toastr.success(response.message);
                            html5QrcodeScanner.resume();
                        } else {
                            toastr.error(response.message);
                            html5QrcodeScanner.resume();
                        }

                    },
                    error: function(error) {
                        html5QrcodeScanner.resume();
                        // Handle error
                        toastr.error('Error: Unable to process the scan.');
                    }
                });




        }
        html5QrcodeScanner.render(onScanSuccess);


       $(document).ready(function(){
            $("#html5-qrcode-button-camera-stop").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-permission").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-start").addClass('btn btn-info');

       })
        // Open camera on button click
    </script>
@endsection
