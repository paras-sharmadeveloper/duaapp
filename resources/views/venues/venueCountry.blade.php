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
                        <input type="hidden" name="iso"  id="iso" value="{{ (isset($venue)) ? $venue->iso : '' }}">  
                        <input type="hidden" name="country_name"  value="{{ (isset($venue)) ? $venue->country_name : '' }}" id="country_name"> 
                        <select id="country" name="country_id" class="form-control js-states form-control">
                            <option value="">select</option>
                            @foreach ($countryList as $country)

                         
                                <option value="{{ $country->id }}" data-name="{{  $country->name }}" 
                                    @if(isset( $venue ) && $venue->country_name == $country->name)
                                        selected
                                    @endif
                                    data-iso="{{ $country->iso2 }}"> {{ $country->name }}</option> 
                            @endforeach
                        </select>
                        
                    </div>

                    <div class="col-md-3 act-btn">
                        <label for="flag_path">Upload Country Flag (Optional) 
                            <a target="_blank" href="https://www.softicons.com/web-icons/flag-icons-by-custom-icon-design">Get Icon (48 * 48)</a> 
                            <br>
                            @if (isset( $venue->flag_path ) && Storage::disk('s3_general')->exists('flags/' . $venue->flag_path))
                            <img src="{{ env('AWS_GENERAL_PATH').'flags/'.$venue->flag_path }}" 
                                alt="Flag Image" style="height: 100px;margin-top:10px">
                            @else
                                <img src="https://i.postimg.cc/wM1GG6qv/avatar.png"  style="height: 100px; "  alt="City Image" id="flag_image_preview" class="flag-image-preview">
                            @endif
                        </label> 
                        <input  type="file" class="form-control-file" id="flag_path" name="flag_path" accept="image/png, image/gif, image/jpeg" style="display: none;">
                    </div>

                    <div class="col-md-6">
                        <label for="flag_path">Upload Country Flag (Optional) 
                            <a target="_blank" href="https://www.softicons.com/web-icons/flag-icons-by-custom-icon-design">Get Icon (48 * 48)</a> 
                        </label><br>
                        <input type="file" class="form-control-file" id="flag_path" name="flag_path">
                        @if (isset( $venue->flag_path ) && Storage::disk('s3_general')->exists('flags/' . $venue->flag_path))
                        <img src="{{ env('AWS_GENERAL_PATH').'flags/'.$venue->flag_path }}" 
                            alt="Flag Image" style="height: 100px;margin-top:10px">
                        @endif

                    </div> 
                </div>
 
               
                 @if(!empty($editData))
                    @foreach($editData['all'] as $l => $venueCity)
                  
                    <div class="row mt-4">
                        <input type="hidden" name="venue_city_id[]" value="{{ $venueCity['id'] }}">
                        <div class=" col-md-3 form-group">
                            <label for="state">State:</label>
                            <input type="hidden" name="state_name[]" value="{{ $editData['state_name'][$l] }}" class="state_name">  
                            <select class="form-control state" id="state" name="state_id[]" >
                                <option value="">Select Country First</option>
                                @foreach($editData['states'] as $state)
                                <option value="{{ $state['id'] }}"
                                @if($venueCity['state_id'] === $state['id'] ) selected @endif
                                >{{ $state['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="col-md-3 form-group">
                            <label for="city">City:</label>
                            
                           <input type="hidden" name="city_name[]" value="{{ $editData['city_name'][$l] }}" class="city_name"> 
                            <select class="form-control city " id="city" name="city_id[]" >
                                <option value="">Select State First</option>
                                @foreach($editData['cities'][$venueCity['state_id']] as $city)
                                    <option value="{{ $city['id'] }}"
                                    @if($venueCity['city_id'] == $city['id'] ) selected @endif
                                    >{{ $city['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-3 act-btn  ">
                            <label for="city">Upload City Flag 48 X 48 :</label>
                            <input type="file" name="city_image[{{ $venueCity['city_id'] }}]" 
                            class="city-image"
                            value="{{ (isset( $venueCity['city_image'] )) ? $venueCity['city_image'] : ''  }}"
                            accept="image/png, image/gif, image/jpeg"
                            >
                            @if (isset( $venueCity['city_image'] ) && Storage::disk('s3_general')->exists('city_image/' .$venueCity['city_image']))
                            <img class="Img-sh" src="{{ env('AWS_GENERAL_PATH').'city_image/'.$venueCity['city_image'] }}" 
                                alt="Flag Image" style="height: 100px;margin-top:10px">
                            @endif
                        </div>
                       
                        <div class="col-md-3 act-btn mt-4">
                            <button class="btn btn-info add" type="button">Add</button>
                            <button class="btn btn-danger remove " type="button">Remove</button>
                        </div>
                    </div>
                    @endforeach
                 @else
                 <div class="row mt-4">
                    <div class=" col-md-3 form-group">
                        <label for="state">State:</label>
                        <input type="hidden" name="state_name[]" value="" class="state_name">  
                        <select class="form-control state" id="state" name="state_id[]" disabled>
                            <option value="">Select Country First</option>
                        </select>
                    </div>
            
                    <div class="col-md-3 form-group">
                        <label for="city">City:</label>
                        
                       <input type="hidden" name="city_name[]" value="" class="city_name"> 
                        <select class="form-control city " id="city" name="city_id[]" disabled>
                            <option value="">Select State First</option>
                        </select>
                    </div>

                    {{-- <div class="col-md-3 act-btn  ">
                        <label for="city">Upload City Flag 48 X 48 :</label>
                        <input type="file" name="city_image[]" class="city-image"  accept="image/png, image/gif, image/jpeg">
                    </div> --}}
                    <div class="col-md-3 act-btn">
                        <label for="city">Upload City Flag 48 X 48 :</label>
                        <!-- Display small image -->
                        <label for="city_image" class="city-image-label">
                            <img src="https://i.postimg.cc/wM1GG6qv/avatar.png" 
                                style="height: 100px; "
                            alt="City Image" id="city_image_preview" class="city-image-preview">
                        </label>
                        <!-- File input (hidden) -->
                        <input type="file" name="city_image[]" id="city_image" class="city-image" accept="image/png, image/gif, image/jpeg" style="display: none;">
                    </div>
                   
                    <div class="col-md-3 act-btn mt-4">
                        <button class="btn btn-info add" type="button">Add</button>
                        <button class="btn btn-danger remove d-none" type="button">Remove</button>
                        <button class="btn btn-success update" type="button">Update</button>
                    </div>
                </div>
                 @endif 
                <div id="append"> </div>
 
                <button type="submit" class="btn btn-primary mt-4">{{ isset($venue) ? 'Update' : 'Create' }}</button>

                </form>
            </div>

            {{--  add city --}}

 

        </div>
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
 function handleFileSelect(input, previewSelector) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(previewSelector).attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
        $('#city_image').on('change', function () {
            const previewSelector = $(this).closest('.row').find('.city-image-preview');
            handleFileSelect(this, previewSelector);
        });
        $('#flag_path').on('change', function () {
            const previewSelector = $(this).closest('.row').find('.flag-image-preview');
            handleFileSelect(this, previewSelector);
        });

        
$(document).ready(function () {
        // Handle country selection
        $('#country').on('change', function () {
            $this = $(this); 
            var countryId = $(this).val();
            var countryName = $(this).find(":selected").attr('data-name'); 
            var iso = $this.find(":selected").attr('data-iso');
            $("#country_name").val(countryName)
            $("#iso").val(iso)

            // Populate states dropdown based on the selected country
            $.ajax({
                url: "{{ route('get-states') }}",
                type: 'GET',
                data: {country_id: countryId},
                success: function (data) {
                   
                    $('#state').empty().append('<option value="">Select State</option>');
                    $.each(data, function (key, value) {
                        $('#state').append('<option value="' + value.id + '" data-name="' + value.name + '">' + value.name + '</option>');
                    });
                    $('#state').prop('disabled', false);
                    $('#city').empty().append('<option value="">Select State First</option>').prop('disabled', true);
                }
            });
        });
        
        $(document).on('change', ".state",function () {
            $this = $(this); 
            var stateId =  $this.val(); 
            var stateName = $this.find(":selected").attr('data-name'); 
           
            $state = $this.parents('.row').find('.state_name'); 
            $state.val(stateName)
            // Populate cities dropdown based on the selected state
            $.ajax({
                url: "{{ route('get-cities') }}",
                type: 'GET',
                data: {state_id: stateId},
                success: function (data) {
                    $city = $this.parents('.row').find('.city'); 
                    $city.empty().append('<option value="">Select City</option>')
                    // $('#city').empty().append('<option value="">Select City</option>');
                    $.each(data, function (key, value) {
                        $city.append('<option value="' + value.id + '" data-name="' + value.name + '" >' + value.name + '</option>');
                    });
                    $city.prop('disabled', false);
                }
            });
            });
        });
        $(document).on('change', ".city",function () {
            $this = $(this); 
            var cityName = $this.find(":selected").attr('data-name'); 
            $state = $this.parents('.row').find('.city_name'); 
            $state.val(cityName) 
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

        

        $(document).on("click",".add",function(){
            $this = $(this); 
            var html = $this.parents('.row').html();
            // $this.parents('.act-btn').find('.remove').removeClass('d-none')
            $("#append").append('<div class="row">'+html+'</div>');
            $("#append").find(".city-image").attr('name','city_image[]');
            $("#append").find(".Img-sh").hide(); 
           
            $("#append").find(".remove").removeClass('d-none');
        }); 
        $(document).on("click",".remove",function(){
            $this = $(this); 
            var html = $this.parents('.row').remove(); 
        }); 
    </script>
    <script>document.title = 'Add/Edit Countries'; </script>
@endsection 