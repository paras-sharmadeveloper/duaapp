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
#footer{display: none;}
.modal-header {
    display: flex;
    justify-content: center;
}
</style>
    <div class="container">
        <div class="row justify-content-center">
            <a href="{{route('qr.gun.scan')}}" class="btn btn-success">Scan Gun</a>
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-1by1" id="reader"></div>
            </div>
        </div>
        <div class="token-area" >
            <p style="display: none"><b>Token Number is: </b></p> <span></span>
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
                qrbox: 225,
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

                           // toastr.success(response.message);
                            html5QrcodeScanner.resume();
                        } else {
                            $(".token-area").find('p').hide();
                            $('#myModal').modal('toggle');
                            $("#model-body").html(response.printToken)
                            if(!response.print){
                                // $("#printButton").hide();
                                $("#vaild-token").hide();
                                $("#invaild-token").text(response.message).show();
                            }

                           // toastr.error(response.message);
                            html5QrcodeScanner.resume();
                        }

                    },
                    error: function(error) {
                        html5QrcodeScanner.resume();
                        // Handle error
                        toastr.error('Error: Unable to process the scan.');
                    }
                });

                $(document).on("click",".close",function(){
                    $('#myModal').modal('hide');
                })



        }
        html5QrcodeScanner.render(onScanSuccess);







       $(document).ready(function(){
            $("#html5-qrcode-button-camera-stop").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-permission").addClass('btn btn-info');
            $("#html5-qrcode-button-camera-start").addClass('btn btn-info');

       })

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

    //    function printDiv(divId) {
    //         var printContents = document.getElementById(divId).innerHTML;
    //         var originalContents = document.body.innerHTML;

    //         document.body.innerHTML = printContents;

    //         window.print();

    //         document.body.innerHTML = originalContents;
    //     }




        // Open camera on button click
    </script>
@endsection
