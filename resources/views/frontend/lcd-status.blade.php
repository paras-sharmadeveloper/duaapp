@extends('layouts.guest')
@section('content')
<section id="mainsection">
<div class="container py-4" id="curt-token" data-ring="" data-token="">

    <div class="main-content" id="main-target">

        <div class="d-flex justify-content-center ">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                <img src="{{ asset('assets/theme/img/logo.png') }}" alt="">

            </a>

        </div>

        <div class="row py-8">

            <div class="col-lg-6 col-md-6 col-sm-6">
                <label>Select City</label>
                <select name="city" id="city" class="form-control">
                    <option>Choose City</option>
                    @foreach($distinctCities as $cities)
                    <option>{{$cities}}</option>
                    @endforeach
                </select>

            </div>
             <div class="col-lg-6 col-md-6 col-sm-6">
                <label>Select Date</label>
                <select name="city" id="venueDate" class="form-control">
                    <option>Choose City First</option>

                </select>

            </div>
        </div>
        <div class="row text-center d-flex justify-content-around">

            <form action="" id="form" method="get">
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
    let Url= "{{ route('waiting-queue', ':selectDate') }}".replace(':selectDate', selectDate);
    $("#form").attr('action',Url)
    $("#btnGo").show();
});

</script>

@endsection
