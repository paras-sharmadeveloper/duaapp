<!-- index.blade.php -->

@extends('layouts.app')

@section('content')
 <style>
    .popup{
    margin: auto;
    text-align: center
}
.popup img{
    width: 100px;
    height: 100px;
    cursor: pointer
}
.show{
    z-index: 999;
    display: none;
}
.show .overlay{
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.66);
    position: absolute;
    top: 0;
    left: 0;
}
.show .img-show{
    width: 600px;
    height: 400px;
    background: #FFF;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    overflow: hidden
}
.img-show span{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
    cursor: pointer;
}
.img-show img{
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
/*End style*/

    </style>

    <div class="card">
        @include('alerts')


        <div class="card-body">

            <h5 class="card-title">Manual List for Visitor</h5>


            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>country_code</th>
                        <th>phone </th>
                        <th>User Image </th>
                        <th>Dua Type</th>

                        <th>Message</th>
                        <th>Message Sid</th>
                        <th>Message Sent Status</th>
                        <th>Message Date</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visitorList as $list)
                    @php
                      $loclpath = '/sessionImages/' . date('d-m-Y').'/';
                     @endphp
                       @php
                       $localImage = '';
                       $localImageStroage = 'sessionImages/' . date('d-m-Y').'/'. (!empty($list->recognized_code)) ? $list->recognized_code:'';
                       if (!empty($list->recognized_code) && !Storage::disk('public_uploads')->exists($localImageStroage)) {
                           $localImage = (!empty($list->recognized_code)) ? $list->recognized_code:'';
                       }


                   @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $list->country_code }}</td>
                            <td>{{ $list->phone }}</td>
                            <td>　<div class="popup">
                                <img class="lightgallery" src="{{$loclpath . $localImageStroage}}"  />
                                </div>

                            </td>
                            <td>{{ $list->dua_type }}</td>
                            <td>{{ $list->msg_sid }}</td>
                            <td>{{ $list->msg_sent_status }}</td>
                            <td>{{ $list->msg_date }}</td>

                            <td>
                                <div class="row d-flex py-4">
                                    <button class="btn btn-success approve mb-4" data-id="{{ $list->id }}"> Approve </button>
                                    <button class="btn btn-danger disapprove  " data-id="{{ $list->id }}"> Disapprove
                                    </button>
                                </div>


                            </td>



                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>

    <div class="show">
        <div class="overlay"></div>
        <div class="img-show">
          <span>X</span>
          <img src="">
        </div>
      </div>

@endsection

@section('page-script')
     <script>


$(function () {
    "use strict";

    $(".popup img").click(function () {
        var $src = $(this).attr("src");
        $(".show").fadeIn();
        $(".img-show img").attr("src", $src);
        $("#success-alert").hide()
        $("#error-alert").hide()
    });

    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });

});

        $(".approve").click(function() {
            var id = $(this).attr('data-id');
            AjaxCall(id,'approve')
        });

        $(".disapprove").click(function() {
            var id = $(this).attr('data-id');
            AjaxCall(id,'disapprove')
        });


        function AjaxCall(id,type) {
            $.ajax({
                url: "{{ route('booking.manual.approve') }}",
                method: 'POST',
                data: {
                    id: id,
                    type : type,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.status){
                        toastr.success(response.message)
                    }else{
                        toastr.error(response.message)
                    }

                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error(error)
                }
            });
        }



    </script>



@endsection
