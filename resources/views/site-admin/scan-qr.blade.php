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
                qrbox: 200
            });

        function onScanSuccess(decodedText, decodedResult) {

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
                        } else {
                            toastr.error(response.message);
                        }

                    },
                    error: function(error) {
                        // Handle error
                        scanResult.innerHTML = 'Error: Unable to process the scan.';
                    }
                });




        }
        html5QrcodeScanner.render(onScanSuccess);



        // Open camera on button click
    </script>
@endsection
