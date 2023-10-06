@extends('layouts.app')

@section('content')
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

                <div class="col-md-6"> 
                         
                        <select id="country_name" name="country_name" class="form-control js-states form-control">
                            <option value="">select</option>
                            @foreach ($countryList as $country)
                                <option value="{{ $country->name }}" data-iso="{{ $country->iso }}"> {{ $country->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="iso" value="" id="iso">
                      
                        {{-- <input type="text" class="form-control" id="country_name" name="country_name"
                            value="{{ isset($venue) ? $venue->country_name : '' }}" required> --}}
                 

                </div>

                <div class="form-group mt-4">
                    <label for="flag_path">Upload Country Flag (Optional) <a target="_blank" href="https://www.softicons.com/web-icons/flag-icons-by-custom-icon-design">Get Icon (48 * 48)</a> </label>
                    <br><br>
                    <input type="file" class="form-control-file" id="flag_path" name="flag_path">
                    @if (isset( $venue->flag_path ) && Storage::disk('s3_general')->exists('flags/' . $venue->flag_path))
                    <img src="{{ env('AWS_GENERAL_PATH').'flags/'.$venue->flag_path }}" alt="Flag Image">
                    @endif

                </div>
                {{-- <div class="form-group mt-4">
                    <label for="type">Type</label>
                    <div>
                        <input type="radio" id="on-site" name="type" value="on-site"
                            {{ isset($venue) && $venue->type === 'on-site' ? 'checked' : '' }} required>
                        <label for="on-site">On-site</label>
                    </div>
                    <div>
                        <input type="radio" id="virtual" name="type" value="virtual"
                            {{ isset($venue) && $venue->type === 'virtual' ? 'checked' : '' }} required>
                        <label for="virtual">Virtual</label>
                    </div>
                </div> --}}

                <!-- Address Input Fields --> 

                <button type="submit"
                    class="btn btn-primary mt-4">{{ isset($venue) ? 'Update' : 'Create' }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
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

        $("#country_name").change(function(){
            $this = $(this); 
            var iso; 
            iso = $this.find(":selected").attr('data-iso');
            $("#iso").val(iso)

        })
    </script>
@endsection
