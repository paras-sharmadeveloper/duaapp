@extends('layouts.app')

@section('content')
    <style>
        span.select2-selection.select2-selection--single {
            height: 40px;
        }
        div#errors {
    text-align: center;
    margin-top: 10px;
    color: red;
}
.mycustom,.country-form {
    max-height: 600px;
    overflow-y: auto;
    padding: 20px 20px;
}
.img-label{
    text-align: center
}
    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> <i
                        class="bi bi-skip-backward-circle me-1"></i> Back</a>
            </div>

        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">

                @if (Route::currentRouteName() == 'country.edit')
                    <h5 class="card-title">Edit</h5>

                    <form action="{{ route('country.update', $venue->id) }}" method="POST" enctype="multipart/form-data" class="country-form">
                        @csrf
                        @method('PUT')
                @else
                <form action="{{ route('country.store') }}" method="POST" enctype="multipart/form-data" class="country-form">
                    @csrf
                                    <h5 class="card-title">Create</h5>
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="iso" id="iso" value="{{ isset($venue) ? $venue->iso : '' }}">
                        <input type="hidden" name="country_name" id="country_name"  value="{{ isset($venue) ? $venue->country_name : '' }}"
                            >
                        <select id="country_id" name="country_id" class="form-control js-states form-control">
                            <option value="">select</option>
                            @foreach ($countryList as $country)
                                <option value="{{ $country->id }}" data-name="{{ $country->name }}"
                                    @if (isset($venue) && $venue->country_name == $country->name) selected @endif data-iso="{{ $country->iso }}">
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-4">
                        <label class="img-label" for="flag_path">Upload Country Flag (Optional) <a target="_blank"
                                href="https://www.softicons.com/web-icons/flag-icons-by-custom-icon-design"> Get Icon (48 *
                                48)</a>
                            <br>
                            @if (isset($venue->flag_path) && Storage::disk('s3_general')->exists('flags/' . $venue->flag_path))
                                <img src="{{ env('AWS_GENERAL_PATH') . 'flags/' . $venue->flag_path }}" alt="Flag Image"
                                    style="height: 50px;margin-top:10px">
                            @else
                                <img src="https://i.postimg.cc/wM1GG6qv/avatar.png" style="height: 100px; " alt="City Image"
                                    id="flag_image_preview" class="flag-image-preview">
                            @endif
                        </label>
                        <input type="file" class="form-control-file" id="flag_path" name="flag_path"
                            accept="image/png, image/gif, image/jpeg" style="display: none;">
                    </div>
                    <div class="col-md-4">  <button type="submit" class="btn btn-primary mt-4">{{ isset($venue) ? 'Update' : 'Create' }}</button> </div>
                </div>


                </form>


                @if (Route::currentRouteName() == 'country.edit')
                <div class="mycustom">
                    @if(!empty($venueCityStates))

                    @foreach($venueCityStates as $venueCity)
                    <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $venueCity->venue_id }}">
                        <input type="hidden" name="id" value="{{ $venueCity->id }}">

                        <div class="row mt-4">
                            <div class=" col-md-2 form-group">
                                <label for="state">State:</label>
                                <input type="text" name="state_name" class="state_name form-control" value="{{ $venueCity->state_name }}">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="city">City:</label>
                                <input type="text" name="city_name"  class="city_name form-control" value="{{ $venueCity->city_name }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="city">Date Column to Show:</label>
                                <input type="text" name="columns_to_show"  class="columns_to_show form-control" value="{{ $venueCity->columns_to_show }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="city">City Sequence to Show:</label>
                                <input type="text" name="city_sequence_to_show"  class="city_sequence_to_show form-control" value="{{ $venueCity->city_sequence_to_show }}">
                            </div>


                            <div class="col-md-2 image-container">
                                <label for="city">Upload City Flag 48 X 48 :</label>

                                <input type="file" name="city_image" id="city_image" class="city-image"
                                    accept="image/png, image/gif, image/jpeg" >
                                @if (isset($venueCity->city_image) && !empty($venueCity->city_image) && Storage::disk('s3_general')->exists('city_image/' . $venueCity->city_image))

                                <img src="{{ env('AWS_GENERAL_PATH') . 'city_image/' . $venueCity->city_image }}" class="city-image-preview" alt="Flag Image"
                                        style="height: 100px;margin-top:10px">
                                @endif

                            </div>

                            <div class="col-md-2 act-btn mt-4">
                                {{-- <button class="btn btn-info add" type="button">Add</button> --}}
                                <button type="button"  data-id="{{ $venueCity->id }}" class="btn text-white mt-4 rounded-3 bg-danger remove"
                                    data-loading="removing..." data-success="Done" data-default="Remove">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        style="display:none">
                                    </span>
                                    <b> Remove</b>
                                </button>

                                <button type="button" class="btn text-white mt-4 rounded-3 bg-success update"
                                    data-loading="updating..." data-success="Done" data-default="Update">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        style="display:none">
                                    </span>
                                    <b> Update</b>
                                </button>
                            </div>
                        </div>
                    </form>
                    @endforeach

                @endif



                <div id="form-city-state">
                    <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $venue->id }}">
                        <div class="row mt-4">
                            <div class=" col-md-2 form-group">
                                <label for="state">State:</label>
                                <input type="text" name="state_name" class="state_name form-control">

                            </div>

                            <div class="col-md-2 form-group">
                                <label for="city">City:</label>

                                <input type="text" name="city_name" value="" class="city_name form-control">

                            </div>

                            <div class="col-md-2 form-group">
                                <label for="city">Date Column to Show:</label>
                                <input type="text" name="columns_to_show"  class="columns_to_show form-control" value="">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="city">City Sequence to Show:</label>
                                <input type="text" name="city_sequence_to_show"  class="city_sequence_to_show form-control"
                                 >
                            </div>


                            <div class="col-md-2 image-container">
                                <label for="city">Upload City Flag 48 X 48 :</label>

                                <input type="file" name="city_image" id="city_image" class="city-image"
                                    accept="image/png, image/gif, image/jpeg" >
                                <img src= "" class="city-image-preview d-none" alt="Flag Image"
                                    style="height: 100px;margin-top:10px">
                            </div>

                            <div class="col-md-2 act-btn ">
                                {{-- <button class="btn btn-info add" type="button">Add</button> --}}
                                <button  type="button" class="btn text-white mt-4 rounded-3 bg-danger remove d-none"
                                    data-loading="removing..." data-success="Done" data-default="Remove">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        style="display:none">
                                    </span>
                                    <b> Remove</b>
                                </button>
                                <button type="button" class="btn text-white mt-4 rounded-3 bg-success update"
                                    data-loading="updating..." data-success="Done" data-default="Update">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        style="display:none">
                                    </span>
                                    <b> Update</b>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="new-form">

                </div>
                    <div id="errors" class="error">
                    </div>
                </div>
                @endif
            </div>

            {{--  add city --}}



        </div>
    </div>


    <div id="form-city-state-fresh" style="display: none">
        <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="venue_id" value="{{ isset($venue) ? $venue->id : '' }}">
            <div class="row mt-4">
                <div class=" col-md-2 form-group">
                    <label for="state">State:</label>
                    <input type="text" name="state_name" class="state_name form-control">

                </div>

                <div class="col-md-2 form-group">
                    <label for="city">City:</label>

                    <input type="text" name="city_name" value="" class="city_name form-control">

                </div>

                <div class="col-md-2 form-group">
                    <label for="city">Date Column to Show:</label>
                    <input type="text" name="columns_to_show"  class="columns_to_show form-control" value="">
                </div>
                <div class="col-md-2 form-group">
                    <label for="city">City Sequence to Show:</label>
                    <input type="text" name="city_sequence_to_show"  class="city_sequence_to_show form-control"
                     >
                </div>

                <div class="col-md-2 image-container">
                    <label for="city">Upload City Flag 48 X 48 :</label>

                    <input type="file" name="city_image" id="city_image" class="city-image"
                        accept="image/png, image/gif, image/jpeg" >
                    <img src= "" class="city-image-preview d-none" alt="Flag Image"
                        style="height: 100px;margin-top:10px">
                </div>

                <div class="col-md-2 act-btn ">
                    {{-- <button class="btn btn-info add" type="button">Add</button> --}}
                    <button  type="button" class="btn text-white mt-4 rounded-3 bg-danger remove d-none"
                        data-loading="removing..." data-success="Done" data-default="Remove">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                            style="display:none">
                        </span>
                        <b> Remove</b>
                    </button>
                    <button type="button" class="btn text-white mt-4 rounded-3 bg-success update"
                        data-loading="updating..." data-success="Done" data-default="Update">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                            style="display:none">
                        </span>
                        <b> Update</b>
                    </button>
                </div>
            </div>
        </form>
    </div>








@endsection
@section('page-script')
    <script type="text/javascript">
        var imageURl = "{{ env('AWS_GENERAL_PATH') . 'city_image/' }}";
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
        $(document).on('click', ".remove", function() {
            $this = $(this);
            var loadingText = $this.attr('data-loading');
            var successText = $this.attr('data-success');
            var defaultText = $this.attr('data-default');

            var id = $this.attr('data-id');
            $this.find('span').show()
            $this.find('b').text(loadingText)
            if(id){
                    $.ajax({
                    url: "{{ route('remove-city-state') }}",
                    type: 'POST',
                    data: { 'id' : id},
                    success: function(data) {
                        $this.find('span').hide()
                        $this.find('b').text(defaultText)
                        $this.parents('form').fadeOut();
                        // You can update the UI or show a success message
                    },
                    error: function(error) {
                        var errors = error.responseJSON.errors;
                        $this.find('b').text(defaultText)
                        $this.find('span').hide()
                        $.each(errors,function(i,err){
                            console.error('Error:', err);
                        });

                    }
                });
            }else{
                $this.find('span').hide()
                $this.find('b').text(defaultText)
                $this.parents('form').fadeOut();
            }


        });

    $(document).on('click', ".update", function() {
        $this = $(this)

        var loadingText = $this.attr('data-loading');
        var successText = $this.attr('data-success');
        var defaultText = $this.attr('data-default');
            var form = $(this).parents('form');
            // Serialize form data
            const formData = new FormData(form[0]);
            $this.find('span').show()
            $this.find('b').text(loadingText)
            $imageCon = $this.parents('.row').find('.image-container > img');
            // Send data to the server using AJAX
            $.ajax({
                url: "{{ route('add-city-state') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $("#errors").empty();
                    $imageCon.attr('src',imageURl+response.image)
                    $imageCon.removeClass('d-none')
                    $this.find('span').hide()
                    $this.find('b').text(defaultText)
                    $this.parents('.act-btn').find('.remove').removeClass('d-none').attr('data-id',response.id)
                    // Handle the response data here
                    if(response.update == false){
                        var form = $("#form-city-state-fresh").html();
                        $(".new-form").append(form)
                        $(".new-form").find(".image-container>img").removeClass('d-none')
                        // $(".new-form").find(".remove").attr('data-id','');
                    }

                    // You can update the UI or show a success message
                },
                error: function(error) {
                    var errors = error.responseJSON.errors;
                    $this.find('b').text(defaultText)
                    $this.find('span').hide()
                    $("#errors").empty();
                    $.each(errors,function(i,err){
                        $("#errors").append("<p>"+err+"</p>");
                        console.error('Error:', err);
                    });

                }
            });
        });






        // Handle state selection

        $(document).ready(function() {
            var addAddressButton = $(".add-address");
            var venueAddresses = $(".venue-addresses");
            var venuehtml = $("#venue-htm").html();
            $(document).on('click', ".add-address", function() {
                venueAddresses.append('<div class="row g-3 mt-3">' + venuehtml + '</div>');
                $(".venue-addresses").find('.remove-address').removeClass('d-none');
            })


            venueAddresses.on("click", ".remove-address", function() {
                $(this).closest('.row').remove();
            });
        });



        $(document).on("click", ".add", function() {
            $this = $(this);
            var html = $this.parents('.row').html();
            // $this.parents('.act-btn').find('.remove').removeClass('d-none')
            $("#append").append('<div class="row">' + html + '</div>');
            $("#append").find(".city-image").attr('name', 'city_image[]');
            $("#append").find(".Img-sh").hide();

            $("#append").find(".remove").removeClass('d-none');
        });

        $("#country_id").change(function(){
            $this = $(this);
            var text = $this.find(":selected").text();
            var iso = $this.find(":selected").attr('data-iso');
            $("#country_name").val(text.replace(/ /g,''))
            $("#iso").val(iso.replace(/ /g,''))
            console.log("iso",iso)
        })

    </script>
    <script>
        document.title = 'Add/Edit Countries';
    </script>
@endsection
