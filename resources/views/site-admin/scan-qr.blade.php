@extends('layouts.app')

@section('content')


 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <video id="videoContainer" class="p-1 border d-none w-100" style="height: 500px;"></video>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="col-md-6 text-center">
            <button class="btn btn-primary" id="startScan">Start Scan</button>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="col-md-6 text-center">
            <div id="scanResult">
                <div class="alert ">

                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('page-script')
{{-- <script src="{{ asset('assets/js/instascan.min.js') }}"></script> --}}
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    document.addEventListener('DOMContentLoaded', function () {
      const videoContainer = document.getElementById('videoContainer');
      const scanResult = document.getElementById('scanResult');
      let scanner = null;
  
      // Initialize instascan
      scanner = new Instascan.Scanner({ video: videoContainer });
      console.log("scanner",scanner)
      // Handle scan result
      scanner.addListener('scan', function (bookingId) {
        console.log("content",content)
        $.ajax({
          url: "{{ route('process-scan') }}",
          method: 'POST',
          data: { id : bookingId ,type: 'verify' },
          success: function (response) {
            // Handle success
            if(response.status){
                $("#scanResult").find("alert").addClass('alert-success').removeClass('alert-danger').text("Confirmed");     
            }else{
                $("#scanResult").find("alert").addClass('alert-danger').removeClass('alert-success').text("Not vaild or expired");   
            }
           
          },
          error: function (error) {
            // Handle error
            scanResult.innerHTML = 'Error: Unable to process the scan.';
          }
        });
      });
  
      // Open camera on button click
      document.getElementById('startScan').addEventListener('click', function () {
        Instascan.Camera.getCameras().then(function (cameras) {
          if (cameras.length > 0) {
            $("#videoContainer").removeClass('d-none')
            scanner.start(cameras[0]);
          } else {
            $("#videoContainer").addClass('d-none')
            alert('No cameras found.');
          }
        });
      });
    });
  </script>



@endsection