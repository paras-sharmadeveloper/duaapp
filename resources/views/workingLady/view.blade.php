<!-- resources/views/employees/show.blade.php -->

@extends('layouts.app')

@section('content')
    <style>
        .id-card-holder {
            width: 225px;
            padding: 4px;
            margin: 0 auto;
            background-color: #1f1f1f;
            border-radius: 5px;
            position: relative;
        }

        .id-card-holder:after {
            content: '';
            width: 7px;
            display: block;
            background-color: #0a0a0a;
            height: 100px;
            position: absolute;
            top: 105px;
            border-radius: 0 5px 5px 0;
        }

        .id-card-holder:before {
            content: '';
            width: 7px;
            display: block;
            background-color: #0a0a0a;
            height: 100px;
            position: absolute;
            top: 105px;
            left: 222px;
            border-radius: 5px 0 0 5px;
        }

        .id-card {

            background-color: #fff;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 1.5px 0px #b9b9b9;
        }

        .id-card img {
            margin: 0 auto;
        }



        .photo img {
            width: 80px;
            margin-top: 15px;
        }

        h2 {
            font-size: 15px;
            margin: 5px 0;
        }

        h3 {
            font-size: 12px;
            margin: 2.5px 0;
            font-weight: 300;
        }

        .qr-code img {
            width: 50px;
        }

        .id-card-holder p {
            font-size: 5px;
            margin: 2px;
        }

        .id-card-hook {
            background-color: black;
            width: 70px;
            margin: 0 auto;
            height: 15px;
            border-radius: 5px 5px 0 0;
        }

        .id-card-hook:after {
            content: '';
            background-color: white;
            width: 47px;
            height: 6px;
            display: block;
            margin: 0px auto;
            position: relative;
            top: 6px;
            border-radius: 4px;
        }

        .id-card-tag-strip {
            width: 45px;
            height: 40px;
            background-color: #d9300f;
            margin: 0 auto;
            border-radius: 5px;
            position: relative;
            top: 9px;
            z-index: 1;
            border: 1px solid #a11a00;
        }

        .id-card-tag-strip:after {
            content: '';
            display: block;
            width: 100%;
            height: 1px;
            background-color: #a11a00;
            position: relative;
            top: 10px;
        }

        .id-card-tag {
            width: 0;
            height: 0;
            border-left: 100px solid transparent;
            border-right: 100px solid transparent;
            border-top: 100px solid #d9300f;
            margin: -10px auto -30px auto;

        }

        .id-card-tag:after {
            content: '';
            display: block;
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-top: 100px solid white;
            margin: -10px auto -30px auto;
            position: relative;
            top: -130px;
            left: -50px;
        }
    </style>
    <div class="container-fluid">


        <div class="card">
            <h1 class="text-center py-3 mt-2">Working Lady Details</h1>
            <div class="card-body">
                <form>
                    {{-- <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="assets/img/profile-img.jpg" alt="Profile">
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div> --}}

                    <div class="row mb-3">
                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                        <div class="col-md-8 col-lg-9">

                            <input name="fullName" type="text" class="form-control" id="fullName"
                                value="{{ $data->first_name }} {{ $data->last_name }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="about" class="col-md-4 col-lg-3 col-form-label">Designation</label>
                        <div class="col-md-8 col-lg-9">
                            {{ $data->designation }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="company" class="col-md-4 col-lg-3 col-form-label">Employer</label>
                        <div class="col-md-8 col-lg-9">
                            {{ $data->employer_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="Job" class="col-md-4 col-lg-3 col-form-label">Place of Work</label>
                        <div class="col-md-8 col-lg-9">
                            {{ $data->place_of_work }}
                        </div>
                    </div>

                    {{-- <div class="row mb-3">
                        <label for="Country" class="col-md-4 col-lg-3 col-form-label">Employee ID Image</label>
                        <div class="col-md-8 col-lg-9">
                            <img src="{{ env('AWS_GENERAL_PATH') . 'employee_ids/' . $data->employee_id_image }}"
                                alt="Employee ID Image" style="max-width: 200px;">
                        </div>
                    </div> --}}

                    {{-- <div class="row mb-3">
                        <label for="Country" class="col-md-4 col-lg-3 col-form-label">Passport Photo</label>
                        <div class="col-md-8 col-lg-9">
                            <img src="{{ env('AWS_GENERAL_PATH') . 'passport_photos/' . $data->passport_photo }}"
                                alt="Passport Image" style="max-width: 200px;">
                        </div>
                    </div> --}}
                    <div class="row mb-3">
                        <label for="Country" class="col-md-4 col-lg-3 col-form-label">Session Image Photo</label>
                        <div class="col-md-8 col-lg-9">
                            <img src="data:image/jpeg;base64,{{ (!empty($data->session_image)) ? base64_encode(getImagefromS3($data->session_image)) : '' }}"
                                alt="Session Image" style="max-width: 200px;">
                        </div>
                    </div>


                    <div class="row mb-3">
                        <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Mobile</label>
                        <div class="col-md-8 col-lg-9">
                            <input name="phone" type="text" class="form-control" id="Phone"
                                value="{{ $data->mobile }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                        <div class="col-md-8 col-lg-9">
                            <input name="email" type="email" class="form-control" id="Email"
                                value="{{ $data->email }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Status</label>
                        <div class="col-md-8 col-lg-9">
                            @if ($data->is_active == 'active')
                                <span class="badge badge-success text-success"> Approved</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Or Code ID</label>
                        <div class="col-md-8 col-lg-9">
                            <div class="qr-code text-center">
                                @if ($data->is_active == 'active')
                                    {!! QrCode::size(200)->generate($data->mobile) !!}
                                    <a href="{{ route('working.lady.qr', $data->qr_id) }}?filename={{$data->first_name.'_' .$data->last_name}}"
                                        class="btn btn-primary float-end">Download QR
                                        Code</a>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Actions</label>
                        <div class="col-md-8 col-lg-9">

                            @if ($data->is_active == 'inactive')
                            <form  >
                            </form>

                                <form  action="{{ route('working.lady.approve', $data->id) }}" method="POST" id="form-approve">
                                    @csrf
                                    <select class="form-control  mb-3" name="type">
                                        <option @if($data->type == 'critical') selected @endif value="critical"> Critical </option>
                                        <option  @if($data->type == 'normal') selected @endif value="normal" > Normal </option>
                                    </select>
                                    <input type="hidden" value="" id="formType" name="formType">
                                    <button type="button" class="btn btn-success approve">Approve</button>
                                    <button type="button" class="btn btn-danger disapprove">Reject</button>
                                </form>
                            @else
                            <form  >
                            </form>
                                <form action="{{ route('working.lady.approve', $data->id) }}" method="POST" id="form-approve">
                                    @csrf
                                    <select class="form-control mb-3" name="type">
                                        <option  @if($data->type == 'critical') selected @endif value="critical"> Critical </option>
                                        <option  @if($data->type == 'normal') selected @endif value="normal" > Normal </option>
                                    </select>
                                    <input type="hidden" value="inactive" id="formType" name="formType">
                                    <button type="button" class="btn btn-danger disapprove">Reject</button>
                                </form>
                            @endif
                        </div>
                    </div>




            </div>
        </div>
    </div>
@endsection

@section('page-script')

<script>
    $(".disapprove").click(function(){
        $("#formType").val('inactive')
        $("#form-approve").submit();
    })
    $(".approve").click(function(){
        $("#formType").val('active')
        $("#form-approve").submit();
    })
</script>

@endsection
