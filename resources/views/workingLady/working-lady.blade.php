@extends('layouts.guest')

@section('content')

    <div class="container main-content">
        <div class="d-flex justify-content-center py-4">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                <img src="https://kahayfaqeer.org/assets/kahe-faqeer.png" alt="">

                <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
            </a>
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
        <h2>Registration Form For Working Lady</h2>
        <form action="{{ route('working.lady.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="designation">Designation:</label>
                <input type="text" class="form-control" id="designation" name="designation" required>
            </div>
            <div class="form-group">
                <label for="employerName">Employer Name / Your Company Name:</label>
                <input type="text" class="form-control" id="employerName" name="employerName" required>
            </div>
            <div class="form-group">
                <label for="placeOfWork">Place of Work (City):</label>
                <input type="text" class="form-control" id="placeOfWork" name="placeOfWork" required>
            </div>
            <div class="form-group mt-3">
                <label for="employeeId">Upload Your Employee ID Image / Employment Proof:</label>
                <input type="file" class="form-control-file" id="employeeId" name="employeeId" accept="image/*" required>
            </div>
            <div class="form-group mt-3">
                <label for="passportPhoto">Your Recent Passport Size Photo:</label>
                <input type="file" class="form-control-file" id="passportPhoto" name="passportPhoto" accept="image/*"
                    required>
            </div>
            <div class="form-group mt-3">
                <label for="mobile">Your Mobile:</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" pattern="[0-9]{10}" required>
            </div>
            <div class="form-group mt-3">
                <label for="email">Your Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group mt-3">
                <label for="email">Why we consider you as Working Lady ? </label>
                <textarea name="why_consider_you_as_working_lady" id="" cols="30" rows="10" class="form-control"></textarea>

            </div>
            <button type="submit" class="btn btn-primary mt-4 float-center">Submit</button>
        </form>
    </div>

@endsection
