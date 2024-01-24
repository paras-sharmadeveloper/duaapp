@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div id="cameraButtons" class="mb-3">
                <!-- Camera buttons will be dynamically populated here -->
            </div>
            <video id="videoContainer" class="p-1 border d-none w-100" style="height: 500px;"></video>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="col-md-6 text-center">
            <div id="scanResult">
                <div class="alert"></div>
            </div>
            {{-- <button class="btn btn-primary" id="startScan">Start Scan</button> --}}
        </div>
    </div>
</div>

@endsection

@section('page-script')
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

        // Handle scan result
        scanner.addListener('scan', function (bookingId) {
            $.ajax({
                url: "{{ route('process-scan') }}",
                method: 'POST',
                data: { id: bookingId, type: 'verify' },
                success: function (response) {
                    if (response.status) {
                        $("#scanResult").find("alert").addClass('alert-success').removeClass('alert-danger').text("Confirmed");
                    } else {
                        $("#scanResult").find("alert").addClass('alert-danger').removeClass('alert-success').text("Not valid or expired");
                    }
                },
                error: function (error) {
                    scanResult.innerHTML = 'Error: Unable to process the scan.';
                }
            });
        });

        // Populate and create camera buttons
        Instascan.Camera.getCameras().then(function (cameras) {
            const cameraButtonsContainer = document.getElementById('cameraButtons');

            cameras.forEach(function (camera, index) {
                const button = document.createElement('button');
                button.classList.add('btn', 'btn-primary', 'mx-2' ,'camera-btn' ,'py-2');
                const cameraType = index === 0 ? 'Front' : 'Back';
                 button.textContent = `Camera ${index + 1} (${cameraType})`;
                 console.log("camera",camera)
                 button.setAttribute('data-camera', camera.id);
                // button.textContent = 'Camera ' + (index + 1);

               

                cameraButtonsContainer.appendChild(button);
            });
        });

        $(".camera-btn").click(function(){
            var camera = $(this).attr('data-camera')
            $("#videoContainer").removeClass('d-none');
            scanner.start(camera);
        }); 

        

       
    });
</script>
@endsection
