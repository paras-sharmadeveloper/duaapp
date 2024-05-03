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
        <h2>Working Lady Details</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Name: {{ $data->first_name }} {{ $data->last_name }} @if ($data->is_active == 'active')
                        <span class="badge badge-success text-success"> Approved</span>
                    @endif
                </h5>
                <p class="card-text">Designation: {{ $data->designation }}</p>
                <p class="card-text">Employer: {{ $data->employer_name }}</p>
                <p class="card-text">Place of Work: {{ $data->place_of_work }}</p>
                <!-- Display employee ID image -->
                <p class="card-text">Employee ID Image:
                    <img src="{{ env('AWS_GENERAL_PATH') . 'employee_ids/' . $data->employee_id_image }}"
                        alt="Employee ID Image" style="max-width: 200px;">
                </p>
                <!-- Display passport photo -->
                <p class="card-text">Passport Photo:
                    <img src="{{ env('AWS_GENERAL_PATH') . 'passport_photos/' . $data->passport_photo }}"
                        alt="Passport Image" style="max-width: 200px;">

                </p>
                <p class="card-text">Mobile: {{ $data->mobile }}</p>
                <p class="card-text">Email: {{ $data->email }}</p>


                @if ($data->is_active == 'active')
                    {{-- <div class="id-card-tag"></div>
                    <div class="id-card-tag-strip"></div>
                    <div class="id-card-hook"></div> --}}
                    <div class="id-card-holder">
                        <div class="id-card">
                            <div class="header" >
                                <img src="https://kahayfaqeer.org/assets/kahe-faqeer.png" style="width:45px;margin-top:5px " alt="">
                            </div>
                            <div class="photo">

                            </div>
                            <h2>{{ $data->first_name }} {{ $data->first_name }}</h2>
                            <div class="qr-code">
                                {!! QrCode::size(80)->generate($data->mobile) !!}
                            </div>
                            <h3>Working Lady</h3>
                            <h3>KF-{{$data->id}}</h3>
                            <hr>
                            <p><strong>NIIT University</strong> Neemrana, NH-8 Delhi-Jaipur highway
                            <p>
                            <p>District Alwar, Rajasthan <strong>301705</strong></p>
                            <p>Ph: 01494-660600, 7073222393</p>

                        </div>
                    </div>


                    <p class="card-text">QR code : </p>
                    <a href="{{ route('working.lady.qr', $data->qr_id) }}" class="btn btn-primary float-end">Download QR
                        Code</a>
                @endif
                        <div class="d-flex justify-content-around">

                            @if ($data->is_active == 'inactive')
                            <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                                @csrf
                                <input type="hidden" value="active" name="formType">
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                            <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                                @csrf
                                <input type="hidden" value="inactive" name="formType">
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        @else
                            <p class="text-success badge badge-success fs-2">Approved</p>
                            <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                                @csrf
                                <input type="hidden" value="inactive" name="formType">
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        @endif

                        </div>

            </div>
        </div>
    </div>
@endsection
