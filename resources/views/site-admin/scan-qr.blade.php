@extends('layouts.app')

@section('content')


 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <label for="cameraSelect">Select Camera:</label>
            <select id="cameraSelect" class="form-select">
                <!-- Camera options will be dynamically populated here -->
            </select>
            <br>
            <video id="videoContainer" class="p-1 border d-none w-100" style="height: 300px; max-width: 100%;"></video>
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
    //   const videoContainer = document.getElementById('videoContainer');
    //   const scanResult = document.getElementById('scanResult');
    //   let scanner = null;
  
    //   // Initialize instascan
    //   scanner = new Instascan.Scanner({ video: videoContainer });
    //   console.log("scanner",scanner)
    //   // Handle scan result
    //   scanner.addListener('scan', function (bookingId) {
    //     console.log("content",content)
    //     $.ajax({
    //       url: "{{ route('process-scan') }}",
    //       method: 'POST',
    //       data: { id : bookingId ,type: 'verify' },
    //       success: function (response) {
    //         // Handle success
    //         if(response.status){
    //             $("#scanResult").find("alert").addClass('alert-success').removeClass('alert-danger').text("Confirmed");     
    //         }else{
    //             $("#scanResult").find("alert").addClass('alert-danger').removeClass('alert-success').text("Not vaild or expired");   
    //         }
           
    //       },
    //       error: function (error) {
    //         // Handle error
    //         scanResult.innerHTML = 'Error: Unable to process the scan.';
    //       }
    //     });
      
  
      // Open camera on button click
    //   document.getElementById('startScan').addEventListener('click', function () {
    //     Instascan.Camera.getCameras().then(function (cameras) {
    //         console.log("cameras",cameras[0])
    //         alert(cameras[0])
    //       if (cameras.length > 0) {
    //         $("#videoContainer").removeClass('d-none')
    //         scanner.start(cameras[0]);
    //       } else if (cameras.length > 1) {
    //         $("#videoContainer").removeClass('d-none')
    //         scanner.start(cameras[1]);
    //       } else {
            
    //         $("#videoContainer").addClass('d-none')
    //         alert('No cameras found.');
    //       }
    //     });
    //   });
    // });

    const videoContainer = document.getElementById("videoContainer");


    const cameraSelect = document.getElementById("cameraSelect");


    function enumerateDevices() {
            navigator.mediaDevices.enumerateDevices()
                .then(devices => {
                    // Filter out video input devices (cameras)
                    const cameras = devices.filter(device => device.kind === 'videoinput');

                    // Populate the camera options in the select element
                    cameras.forEach(camera => {
                        const option = document.createElement("option");
                        option.value = camera.deviceId;
                        option.text = camera.label || `Camera ${cameraSelect.options.length + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    // Initialize the video stream with the first available camera
                    if (cameras.length > 0) {
                        startVideo(cameras[0].deviceId);
                    }
                })
                .catch(error => {
                    console.error('Error enumerating devices:', error);
                });
        }


    function startVideo(deviceId) {
            const constraints = {
                video: { deviceId: deviceId }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(stream => {
                    videoContainer.srcObject = stream;
                    videoContainer.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error starting video stream:', error);
                });
    }

    cameraSelect.addEventListener("change", function () {
            const selectedDeviceId = this.value;
            startVideo(selectedDeviceId);
        });
        

        enumerateDevices();
    });
  </script>



@endsection