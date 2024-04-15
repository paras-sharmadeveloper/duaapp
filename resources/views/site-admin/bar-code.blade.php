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
            <div class="col-md-6">
                <input id='bCode'type='text' value='barcode appears here' class="form-control w-100"/>
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script> --}}

    <script>
        $(window).ready(function() {

                    //$("#bCode").scannerDetection();

                    console.log('all is well');

                    $(window).scannerDetection();
                    $(window).bind('scannerDetectionComplete', function(e, data) {
                            console.log('complete ' + data.string);
                            $("#bCode").val(data.string);
                        })
                        .bind('scannerDetectionError', function(e, data) {
                            console.log('detection error ' + data.string);
                        })
                        .bind('scannerDetectionReceive', function(e, data) {
                            console.log('Recieve');
                            console.log(data.evt.which);
                        })
        });
    </script>
@endsection
