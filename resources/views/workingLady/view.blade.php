<!-- resources/views/employees/show.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Working Lady Details</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Name: {{ $data->first_name }} {{ $data->last_name }}  @if($data->is_active == 'active') <span class="badge badge-success text-success"> Approved</span> @endif </h5>
            <p class="card-text">Designation: {{ $data->designation }}</p>
            <p class="card-text">Employer: {{ $data->employer_name }}</p>
            <p class="card-text">Place of Work: {{ $data->place_of_work }}</p>
            <!-- Display employee ID image -->
            <p class="card-text">Employee ID Image:
                <img src="{{ env('AWS_GENERAL_PATH') . 'employee_ids/' .$data->employee_id_image }}" alt="Employee ID Image" style="max-width: 200px;">
            </p>
            <!-- Display passport photo -->
            <p class="card-text">Passport Photo:
                <img src="{{ env('AWS_GENERAL_PATH') . 'passport_photos/' .$data->passport_photo }}" alt="Passport Image" style="max-width: 200px;">

            </p>
            <p class="card-text">Mobile: {{ $data->mobile }}</p>
            <p class="card-text">Email: {{ $data->email }}</p>
            @if($data->is_active == 'active')
            <p class="card-text">QR code : {!! QrCode::size(100)->generate( $data->qr_id  ) !!} </p>
            <a href="{{ route('working.lady.qr', $data->qr_id) }}" class="btn btn-primary float-end">Download QR Code</a>


            @endif
            @if($data->is_active == 'inactive')
            <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                @csrf
                <input type="hidden" value="active" name="formType">
                <button type="submit" class="btn btn-success">Approve Request</button>
            </form>
            <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                @csrf
                <input type="hidden" value="inactive" name="formType">
                <button type="submit" class="btn btn-danger">Reject Request</button>
            </form>
            @else
              <p class="text-success badge badge-success fs-2">Approved</p>
              <form action="{{ route('working.lady.approve', $data->id) }}" method="POST">
                @csrf
                <input type="hidden" value="inactive" name="formType">
                <button type="submit" class="btn btn-danger">Reject Request</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
