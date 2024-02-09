@extends('layouts.app')

@section('content')



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div style="width: 500px" id="reader"></div>

            {{-- <video id="videoContainer" class="p-1 border d-none w-100" style="height: 500px;"></video> --}}
        </div>
    </div>
    {{-- <div class="row justify-content-center mt-3">
        <div class="col-md-6 text-center">
            <button class="btn btn-primary" id="startScan">Start Scan</button>
        </div>
    </div> --}}
    {{-- <div class="row justify-content-center mt-3">
        <div class="col-md-6 text-center">
            <div id="scanResult">
                <div class="alert ">

                </div>
            </div>
        </div>
    </div> --}}
</div>


@endsection

@section('page-script')
{{-- <script src="{{ asset('assets/js/instascan.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
{{-- <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script> --}}

<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var html5QrcodeScanner = new Html5QrcodeScanner(
	"reader", { fps: 2, qrbox: 200 });

    function onScanSuccess(decodedText, decodedResult) {

        $.ajax({
          url: "{{ route('process-scan') }}",
          method: 'POST',
          data: { id : decodedText ,type: 'verify' },
          success: function (response) {
            // Handle success
            if(response.success){
                toastr.success(response.message);
             }else{
                toastr.error(response.message);
             }

          },
          error: function (error) {
            // Handle error
            scanResult.innerHTML = 'Error: Unable to process the scan.';
          }
        });


    // Handle on success condition with the decoded text or result.
        console.log(`Scan result: ${decodedText}`, decodedResult);
    }
html5QrcodeScanner.render(onScanSuccess);



      // Open camera on button click

  </script>



@endsection
