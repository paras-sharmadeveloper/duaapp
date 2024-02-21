@extends('layouts.guest')
@section('content')
<section id="mainsection">
    <style>
        .btn {
            padding: 40px;
            font-size: 60px;
        }
        </style>

<div class="container py-4" id="curt-token" data-ring="" data-token="">

    <div class="main-content" id="main-target">

        <div class="d-flex justify-content-center ">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">

            </a>

        </div>

        <div class="row text-center mt-5">



                @if($getDates->count() > 0)
                <div class="col-lg-12 col-md-6 col-sm-6">
                    @foreach($getDates as $city =>  $id)
                    <form action="{{ route('waiting-queue', $id) }}" id="form" method="get">
                        <button type="submit" id="btnGo" class="btn btn-dark mt-4 btn-xl py-8 w-100">{{$city}} </button>
                    </form>
                </div>
                    @endforeach
                @else
                    <h1> No Venue for Today</h1>
                @endif



        </div>
        <div class="row text-center d-flex justify-content-around">


                <button type="submit" id="btnGo" style="display: none" class="btn btn-info mt-4 btn-xl"> Go to Screen </button>
            </form>

        </div>

    </div>
    </div>
</section>
@endsection


@section('page-script')
<script>
    $("#city").on('change', function(){
	var selectedCountry = $(this).find("option:selected").val();
    	$.ajax({
        	url: "{{ route('status-screen')}}",
            data:{
                city:selectedCountry
            },
        	success: function(response){
        		var html = '';
        		$("#venueDate").html(''); //For clearing old option list
        		for(var i = 0; i < response.length; i++) {
        	    		var obj = response[i];
        	    		html += '<option value="'+obj.id+'">'+obj.venue_date+'</option>';
        		}
        		$("#venueDate").html('<option value="">Date Loaded</option>'+html);

        	}
    	});
});
$(document).on("change", "#venueDate", function() {
    var selectDate = $(this).find("option:selected").val();
    if(selectDate){
        let Url= "{{ route('waiting-queue', ':selectDate') }}".replace(':selectDate', selectDate);
        $("#form").attr('action',Url)
        $("#btnGo").show();
    }else{
        alert("please choose date")
    }

});

</script>

@endsection
