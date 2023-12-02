@extends('layouts.app')

@section('content')
    <style>
        span.select2-selection.select2-selection--single {
            height: 40px;
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

                    {!! Form::model($venue, [
                        'route' => ['country.update', $venue->id],
                        'method' => 'PUT',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                @else
                    {!! Form::open(['route' => 'country.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                    <h5 class="card-title">Create</h5>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="iso" id="iso" value="{{ isset($venue) ? $venue->iso : '' }}">
                        <input type="hidden" name="country_name" value="{{ isset($venue) ? $venue->country_name : '' }}"
                            id="country_name">
                        <select id="country" name="country_id" class="form-control js-states form-control">
                            <option value="">select</option>
                            @foreach ($countryList as $country)
                                <option value="{{ $country->id }}" data-name="{{ $country->name }}"
                                    @if (isset($venue) && $venue->country_name == $country->name) selected @endif data-iso="{{ $country->iso2 }}">
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6 ">
                        <label for="flag_path">Upload Country Flag (Optional) <a target="_blank"
                                href="https://www.softicons.com/web-icons/flag-icons-by-custom-icon-design"> Get Icon (48 *
                                48)</a>
                            <br>
                            @if (isset($venue->flag_path) && Storage::disk('s3_general')->exists('flags/' . $venue->flag_path))
                                <img src="{{ env('AWS_GENERAL_PATH') . 'flags/' . $venue->flag_path }}" alt="Flag Image"
                                    style="height: 100px;margin-top:10px">
                            @else
                                <img src="https://i.postimg.cc/wM1GG6qv/avatar.png" style="height: 100px; " alt="City Image"
                                    id="flag_image_preview" class="flag-image-preview">
                            @endif
                        </label>
                        <input type="file" class="form-control-file" id="flag_path" name="flag_path"
                            accept="image/png, image/gif, image/jpeg" style="display: none;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">{{ isset($venue) ? 'Update' : 'Create' }}</button>

                </form>


                @if (Route::currentRouteName() == 'country.edit')

                @if(!empty($venueCityStates))

                    @foreach($venueCityStates as $venueCity)
                    <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $venueCity->venue_id }}">
                        <input type="hidden" name="id" value="{{ $venueCity->id }}">
                        
                        <div class="row mt-4">
                            <div class=" col-md-3 form-group">
                                <label for="state">State:</label>
                                <input type="text" name="state_name" class="state_name form-control" value="{{ $venueCity->state_name }}">

                            </div>

                            <div class="col-md-3 form-group">
                                <label for="city">City:</label>

                                <input type="text" name="city_name"  class="city_name form-control" value="{{ $venueCity->city_name }}">

                            </div>

                            <div class="col-md-3 image-container">
                                <label for="city">Upload City Flag 48 X 48 :</label>
                               

                                @if (isset($venueCity->city_image) && Storage::disk('s3_general')->exists('city_image/' . $venueCity->city_image))
                                <label for="city_image" class="city-image-label">
                                    <img src="{{ env('AWS_GENERAL_PATH') . 'city_image/' . $venueCity->city_image }}" class="city-image-preview" alt="Flag Image"
                                        style="height: 100px;margin-top:10px">
                                </label>
                                @else
                                <label for="city_image" class="city-image-label">
                                    <img src="https://i.postimg.cc/wM1GG6qv/avatar.png" style="height: 100px; "
                                        alt="City Image" id="city_image_preview" class="city-image-preview">
                                </label>
                                    
                                @endif
                                
                                <input type="file" name="city_image" id="city_image" class="city-image"
                                    accept="image/png, image/gif, image/jpeg" style="display: none;">
                            </div>

                            <div class="col-md-3 act-btn mt-4">
                                {{-- <button class="btn btn-info add" type="button">Add</button> --}}
                                <button class="btn btn-danger remove" data-id="{{ $venueCity->id }}" type="button">Remove</button>
                                <button class="btn btn-success update" type="button">Update</button>
                            </div>
                        </div>
                    </form>
                    @endforeach

                @endif
                <div class="form"  id="form-city-state">
                    <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $venue->id }}">
                        <div class="row mt-4">
                            <div class=" col-md-3 form-group">
                                <label for="state">State:</label>
                                <input type="text" name="state_name" class="state_name form-control">

                            </div>

                            <div class="col-md-3 form-group">
                                <label for="city">City:</label>

                                <input type="text" name="city_name" value="" class="city_name form-control">

                            </div>

                            <div class="col-md-3 image-container">
                                <label for="city">Upload City Flag 48 X 48 :</label>
                                <!-- Display small image -->
                                <label for="city_image" class="city-image-label">
                                    <img src="https://i.postimg.cc/wM1GG6qv/avatar.png" style="height: 100px; "
                                        alt="City Image" id="city_image_preview" class="city-image-preview">
                                </label>
                                <!-- File input (hidden) -->
                                <input type="file" name="city_image" id="city_image" class="city-image"
                                    accept="image/png, image/gif, image/jpeg" style="display: none;">
                            </div>

                            <div class="col-md-3 act-btn mt-4">
                                {{-- <button class="btn btn-info add" type="button">Add</button> --}}
                                <button class="btn btn-danger remove d-none" type="button">Remove</button>
                                <button class="btn btn-success update" type="button">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="new-form">

                </div>
                    
                @endif










            </div>

            {{--  add city --}}



        </div>
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
        
        $(document).on('click', ".remove", function() {   
            $this = $(this); 
            var id = $this.attr('data-id'); 
            $.ajax({
                url: "{{ route('remove-city-state') }}",
                type: 'POST',
                data: {
                    id : id
                },
                processData: false,
                contentType: false,
                success: function(data) {
                     $this.parents('form').fadeOut(); 
                    // You can update the UI or show a success message
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

        });
     
    $(document).on('click', ".update", function() {    
            var form = $(this).parents('form'); 
            // Serialize form data
            const formData = new FormData(form[0]);

            // Send data to the server using AJAX
            $.ajax({
                url: "{{ route('add-city-state') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.update == false){
                        var form = $("#form-city-state").html(); 
                        $(".new-form").append(form)
                    }
                    
                    // Handle the response data here
                    console.log(data);

                    // You can update the UI or show a success message
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });

        function handleFileSelect(input, previewSelector) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewSelector.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
        $(document).on('change', ".city-image", function() {
            $this = $(this);
            const previewSelector = $this.parents('.image-container').find('.city-image-preview');
            handleFileSelect(this, previewSelector);
        });
        $(document).on('change', ".flag_path", function() {

            const previewSelector = $(this).closest('.row').find('.flag-image-preview');
            handleFileSelect(this, previewSelector);
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
        $(document).on("click", ".remove", function() {
            $this = $(this);
            var html = $this.parents('.row').remove();
        });
    </script>
    <script>
        document.title = 'Add/Edit Countries';
    </script>
@endsection
