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
.img-show span {
    color: red;
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
                        {{-- <th>Message</th>
                        <th>Message Sid</th>
                        <th>Message Sent Status</th>
                        <th>Message Date</th> --}}
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
                            <td><div class="popup">
                                <img class="lightgallery" src="{{$loclpath . $localImageStroage}}"  />
                                </div>
                            </td>
                            <td>{{  ucwords($list->dua_type)  }}</td>
                            {{-- <td>{{ $list->message }}</td>
                            <td>{{ $list->msg_sid }}</td>
                            <td>{{ $list->msg_sent_status }}</td>
                            <td>{{ $list->msg_date }}</td> --}}

                            <td>
                                @if(empty($list->action_at))
                                    <div class="row py-4 actionBtns">

                                        <button type="button" class="btn btn-success approve mb-3"
                                            data-id="{{ $list->id }}"
                                            data-loading="Loading..." data-success="Done"
                                            data-default="Approve">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                            </span>
                                            <b>Approve ({{ ucwords($list->dua_type)  }})</b>
                                        </button>

                                        <button type="button" class="btn  btn-danger disapprove"
                                            data-id="{{ $list->id }}"
                                            data-loading="Loading..." data-success="Done"
                                            data-default="Disapprove">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                            </span>
                                            <b>Disapprove  ({{ ucwords($list->dua_type)  }})</b>
                                        </button>
                                    </div>
                                    @else
                                    <p> Action Taken </p>
                                @endif
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
          <span><i class="fa fa-times"></i></span>
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
            AjaxCall(id,'approve',$(this))
        });

        $(".disapprove").click(function() {
            var id = $(this).attr('data-id');
            AjaxCall(id,'disapprove',$(this))
        });


        function AjaxCall(id,type,event) {

            var loadingText = event.attr('data-loading');
            var successText = event.attr('data-success');
            var defaultText = event.attr('data-default');

            event.find('span').show()
            event.find('b').text(loadingText)


            $.ajax({
                url: "{{ route('booking.manual.approve') }}",
                method: 'POST',
                data: {
                    id: id,
                    type : type,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {

                   event.find('span').hide()
                   event.find('b').text(defaultText)

                   event.parents('.actionBtns').fadeOut();

                //    event.parents('tr').fadeOut();


                    if(response.status){
                        toastr.success(response.message)
                    }else{
                        toastr.error(response.message)
                    }

                },
                error: function(xhr, status, error) {
                    event.find('span').hide()
                    event.find('b').text(defaultText)
                    console.error(error);
                    toastr.error(error)
                }
            });
        }



    </script>



@endsection
